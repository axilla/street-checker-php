<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\View\View; #in case that u need view object!
use Nelmio\ApiDocBundle\Annotation\ApiDoc; # important for documentation, must have
use FOS\RestBundle\Controller\Annotations as Rest; # must have for rest anotations
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\SCClasses\SCJsonResponse;
use AppBundle\Entity\User;

class APIAuthorizationController extends Controller
{
    
    /**
     * @Rest\View
     * @ApiDoc(
     *  description="Registering service with full user data. Gray parameters are optional.",
     *  statusCodes = {
     *     201 = "Returned when successful",
     *     403 = "Returned when data are not valid."
     *   },
     *  requirements={    
     *          {"name"="username", "dataType"="string", "description"="", "default" = "test"},
     *          {"name"="email", "dataType"="string", "description"="", "default" = "test@l4m.rs"},
     *          {"name"="password", "dataType"="string", "description"="", "default" = "123"},
     * },
     * parameters={
     *          {"name"="firstName", "dataType"="string", "required"=false, "description"="User first name.", "default"="Test"},
     *          {"name"="lastName", "dataType"="string", "required"=false, "description"="User last name.", "default"="USer"},
     *          {"name"="gender", "dataType"="string", "required"=false, "description"="User gender.", "default"="male"},
     *          {"name"="ProfileImage", "dataType"="file", "required"=false, "description"="Base64 encoded image", "default" = ""},
     *          {"name"="binaryProfileImage", "dataType"="string", "required"=false, "description"="Base64 encoded image", "default" = ""},
     *          {"name"="binaryProfileImageType", "dataType"="string", "required"=false, "description"="Image type(jpg/png/gif/jpeg/bmp)", "default" = ""}
     * }
     * )
     */
    public function registerAction(Request $request)
    {
        /* Get manager */
        $em = $this->getDoctrine()->getManager();
        
        /* Authorization parameters */
        $username = $request->get('username');
        $existedUser = $em->getRepository('AppBundle:User')->findOneBy(array('username'=>$username));
        if($existedUser){
            throw new HttpException(400, 'This username already exist!');
        }
        $email = $request->get('email');
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            throw new HttpException(400, 'Invalid email!');
        }
        $existedUser = $em->getRepository('AppBundle:User')->findOneBy(array('email'=>$email));
        if($existedUser){
            throw new HttpException(400, 'This email already exist!');
        }
        $passwordParam = $request->get('password');
        $firstName = $request->get('firstName');
        $lastName = $request->get('lastName');
        $gender = $request->get('gender');
        
        /* Flooding and Bot behavior check */
//        $behavierChecker = $this->container->get('dangerous_behavior_checker');
//        $dangerousBehavior = $behavierChecker->ipAddressCheck($ip);
//        if(!$dangerousBehavior){
//            return View::create(array('msg' => 'Only one registration per 24 hours from same IP address is allowed!'), 403);
//        }

        /* Create new user */
        $newUser = new User();
        
        $newUser->setFirstName($firstName);
        $newUser->setLastName($lastName);
        $newUser->setUsername($username);
        $newUser->setEmail($email);
        $newUser->setGender($gender);
        $newUser->login();
        
        $newUser->setPassword($passwordParam);
        $newUser->setLastAccess(new \DateTime());

        $em->persist($newUser);
        $em->flush();
        
        //Generate Street Checker Access Token
        $scAccessToken = md5(uniqid($newUser->getId() . time(), true));
        $newUser->setscAccessToken($scAccessToken);
        $em->persist($newUser);
        $em->flush();

        $binaryProfileImage = $request->get('binaryProfileImage');
        $binaryProfileImageType = $request->get('binaryProfileImageType');
        /* If we have binary cover image upload them */
        if ($binaryProfileImage || $binaryProfileImageType) {

            $binaryPhotosTypes = $this->container->getParameter('sc.images.binary.types');
            if (!in_array($binaryProfileImageType, $binaryPhotosTypes)) {
                throw new HttpException(400, 'Invalid type of binary photo!');
            }

            $storePhotos = $this->get('services.store_images');
            $storePhotos->storeProfileBinaryImage($binaryProfileImage, $binaryProfileImageType, $newUser->getId());
        }

        $images = $request->files->get('ProfileImage');
        if($images){
            $storePhotos = $this->get('services.store_images');
            if(!is_array($images)){
                $images = array($images);
            }
            foreach ($images as $image){
                $storePhotos->storeProfileImage($image, $newUser->getId());
            }
        }

        $em->refresh($newUser);
        $serializer = $this->container->get('jms_serializer');
        $user = json_decode($serializer->serialize($newUser, 'json'));

        $response = array();
        $response['message'] = "User successfully registered!";
        $response['user'] = $user;
        $response['user']->scAccessToken = $newUser->getScAccessToken();

        return new SCJsonResponse($response);
    }
    
    /**
     * @Rest\View
     * @ApiDoc(
     *  description="Login service.",
     *  statusCodes = {
     *     200 = "Returned when successful",
     *     403 = "Returned when data are not valid."
     *   },
     *  requirements={    
     *          {"name"="usernameOrEmail", "dataType"="string", "description"="", "default" = ""},
     *          {"name"="password", "dataType"="string", "description"="", "default" = ""},
     * }
     * )
     */
    public function loginAction(Request $request)
    {
        /* Get manager */
        $em = $this->getDoctrine()->getManager();
        
        /* Authorization parameters */
        $usernameOrEmail = $request->get('usernameOrEmail');
        $password = $request->get('password');

        /* Check if user sent email or username */
        if(filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL)){
            $search = array('email' => $usernameOrEmail);
        }else{
            $search = array('username' => $usernameOrEmail);
        }
        
        $user = $em->getRepository('AppBundle:User')->findOneBy($search);
        if(!$user){
            throw new HttpException(401, "User with this username/email ( " . $usernameOrEmail . " ) do not exist.");
        }
        
        /* Password encoding */
        $salt = $user->getSalt();
        $userPass = $user->getPassword();
        
        $encoder = new MessageDigestPasswordEncoder('sha1');
        $encodedPass = $encoder->encodePassword($password, $salt);
        
        if($encodedPass != $userPass){
            return View::create(array('msg'=>'Wrong password or email/username!'), 401);
        }
        
        $user->setLastLogin(new \DateTime());
        $user->setLastAccess(new \DateTime());
        $user->login();
        
        //Generate Street Checker Access Token
        $scAccessToken = md5(uniqid($user->getId() . time(), true));
        $user->setscAccessToken($scAccessToken);
        
        $em->persist($user);
        $em->flush();


        $serializer = $this->container->get('jms_serializer');
        $userNorm = json_decode($serializer->serialize($user, 'json'));

        $response = array();
        $response['message'] = "User successfully Logged IN!";
        $response['user'] = $userNorm;
        $response['user']->scAccessToken = $user->getScAccessToken();

        return new SCJsonResponse($response);
    }
    
    /**
     * @Rest\View
     * @ApiDoc(
     *  description="Logout user from API",
     *  statusCodes = {
     *     200 = "Returned when user successfulu loged out",
     *     404 = "Returned when user is not found!"
     *   }
     * )
     */
    public function logoutAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $loggedUserToken = $request->headers->get('sc-access-token');
        // Get User details 
        $user = $em->getRepository('AppBundle:User')->findOneBy(array('scAccessToken' => $loggedUserToken));
        if(!$user){
            throw new HttpException(404, 'User not found!');
        }

        $user->logout();
        $em->persist($user);
        $em->flush();

        return new SCJsonResponse("User is logged out!");
    }
    
    /**
     * @Rest\View
     * @ApiDoc(
     *  description="Check  Availability of username or email. ( 1 for available, 0 for unvailable",
     *  statusCodes = {
     *     200 = "Returned when successful",
     *   },
     * requirements = {
     *  },
     *  filters={
     *      {"name"="username", "dataType"="string", "description"="Username"},
     *      {"name"="email", "dataType"="string", "description"="Email"},
     *  }
     * 
     * )
     */
    public function checkAvailabilityAction(Request $request)
    {
        /* Get manager */
        $em = $this->getDoctrine()->getManager();
        // Get filters from query string
        $query = $request->query->all();
        
        $username = NULL;
        $email = NULL;
        if(isset($query['username'])){ $username = $query['username']; }
        if(isset($query['email'])){ $email = $query['email']; }
        
        $emailAvailable = NULL;
        $usernameAvailable = NULL;
        
        if($email){
            $existedUserEmail = $em->getRepository('AppBundle:User')->findOneBy(array('email'=>$email));
            if($existedUserEmail){
                $emailAvailable = 0;
            }else{
                $emailAvailable = 1;
            }
        }
        
        if($username){
            $existedUserUsername = $em->getRepository('AppBundle:User')->findOneBy(array('username'=>$username));
            if($existedUserUsername){
                $usernameAvailable = 0;
            }else{
                $usernameAvailable = 1;
            }
        }
        
        return View::create(array('response' => array('username' => $usernameAvailable, 'email' => $emailAvailable)), 200);
    }
    
    
}
