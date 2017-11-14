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

class APIUserController extends Controller
{

    /**
     * @Rest\View
     * @ApiDoc(
     *  description="Show User profile",
     *  statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when User not found"
     *   },
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="Id of Report"}
     *  },
     * )
     */
    public function showAction(Request $request)
    {
        /* Get manager */
        $em = $this->getDoctrine()->getManager();
        
        /* Authorization parameters */
        $id = $request->get('id');
        $user = $em->getRepository('AppBundle:User')->find($id);
        if(!$user){
            throw new HttpException(404, 'User not found!');
        }

        $serializer = $this->container->get('jms_serializer');
        $user = json_decode($serializer->serialize($user, 'json'));

        return new SCJsonResponse($user);
    }

    /**
     * @Rest\View
     * @ApiDoc(
     *  description="Show User profile",
     *  statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when User not found"
     *   },
     *  requirements={
     *  },
     * )
     */
    public function showMeAction(Request $request)
    {
        /* Get manager */
        $em = $this->getDoctrine()->getManager();

        /* Check if logged user is authorized for this action */
        $auth = $this->container->get('services.auth_service');
        $user = $auth->getLoggedUser($request);
        if(!$user){
            throw new HttpException(404, 'User not found!');
        }

        $serializer = $this->container->get('jms_serializer');
        $userSer = json_decode($serializer->serialize($user, 'json'));

        $userSer->scAccessToken = $user->getScAccessToken();

        return new SCJsonResponse($userSer);
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
     *          {"name"="id", "dataType"="integer", "description"="", "default" = ""},
     * },
     * parameters={
     *          {"name"="username", "dataType"="string", "required"=false, "description"="Username of user.", "default"="userBre"},
     *          {"name"="email", "dataType"="string", "required"=false, "description"="User email.", "default"="qweq@qwe.as"},
     *          {"name"="firstName", "dataType"="string", "required"=false, "description"="User first name.", "default"="Test"},
     *          {"name"="lastName", "dataType"="string", "required"=false, "description"="User last name.", "default"="USer"},
     *          {"name"="gender", "dataType"="string", "required"=false, "description"="User gender.", "default"="male"},
     * }
     * )
     */
    public function updateAction(Request $request)
    {
        /* Get manager */
        $em = $this->getDoctrine()->getManager();
        /* Get User */
        $id = $request->get('id');
        $user = $em->getRepository('AppBundle:User')->findOneBy(array('id'=> $id));
        if(!$user){
            throw new HttpException(404, "User not found.");
        }

        /* Get Validator */
        $validator = $this->container->get('helpers.input_validator');

        $username = $validator->check($request->get('username'), 'string', 'username');
        if($username) $user->setUsername($username);

        $email = $validator->check($request->get('email'), 'string', 'email');
        if($email) $user->setEmail($email);

        $firstName = $validator->check($request->get('firstName'), 'string', 'firstName');
        if($firstName) $user->setFirstName($firstName);

        $lastName = $validator->check($request->get('lastName'), 'string', 'lastName');
        if($lastName) $user->setLastName($lastName);

        $gender = $validator->check($request->get('gender'), 'string', 'gender');
        if($gender) $user->setGender($gender);

        $em->persist($user);
        $em->flush();

        $serializer = $this->container->get('jms_serializer');
        $userSer = json_decode($serializer->serialize($user, 'json'));

        return new SCJsonResponse($userSer);
    }
    /**
     * @Rest\View
     * @ApiDoc(
     *  description="Login service.",
     *  statusCodes = {
     *     200 = "Returned when successful",
     *     403 = "Returned when data are not valid."
     *   },
     * parameters={
     *          {"name"="username", "dataType"="string", "required"=false, "description"="Username of user.", "default"="userBre"},
     *          {"name"="email", "dataType"="string", "required"=false, "description"="User email.", "default"="qweq@qwe.as"},
     *          {"name"="firstName", "dataType"="string", "required"=false, "description"="User first name.", "default"="Test"},
     *          {"name"="lastName", "dataType"="string", "required"=false, "description"="User last name.", "default"="USer"},
     *          {"name"="gender", "dataType"="string", "required"=false, "description"="User gender.", "default"="male"},
     * }
     * )
     */
    public function updateMeAction(Request $request)
    {
        /* Get manager */
        $em = $this->getDoctrine()->getManager();

        /* Check if logged user is authorized for this action */
        $auth = $this->container->get('services.auth_service');
        $user = $auth->getLoggedUser($request);
        if(!$user){
            throw new HttpException(404, "User not found!");
        }

        /* Get Validator */
        $validator = $this->container->get('helpers.input_validator');

        $username = $validator->check($request->get('username'), 'string', 'username');
        if($username) $user->setUsername($username);

        $email = $validator->check($request->get('email'), 'string', 'email');
        if($email) $user->setEmail($email);

        $firstName = $validator->check($request->get('firstName'), 'string', 'firstName');
        if($firstName) $user->setFirstName($firstName);

        $lastName = $validator->check($request->get('lastName'), 'string', 'lastName');
        if($lastName) $user->setLastName($lastName);

        $gender = $validator->check($request->get('gender'), 'string', 'gender');
        if($gender) $user->setGender($gender);

        $em->persist($user);
        $em->flush();

        $serializer = $this->container->get('jms_serializer');
        $userSer = json_decode($serializer->serialize($user, 'json'));

        $userSer->scAccessToken = $user->getScAccessToken();

        return new SCJsonResponse($userSer);
    }
}
