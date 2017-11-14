<?php
namespace AppBundle\Controller;
/**
 * Created by PhpStorm.
 * User: axe
 * Date: 27.11.16.
 * Time: 18.56
 */

use AppBundle\Entity\Comment;
use AppBundle\Entity\Impression;
use AppBundle\Entity\Report;
use AppBundle\SCClasses\SCJsonResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc; # important for documentation, must have
use FOS\RestBundle\View\View; #in case that u need view object!
use FOS\RestBundle\Controller\Annotations as Rest; # must have for rest anotations

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpKernel\Exception\HttpException;

class APITestController  extends FOSRestController
{

    /**
     * @Rest\View
     * @ApiDoc(
     *  description="Like the report.",
     *  statusCodes = {
     *     200 = "Returned when successful",
     *   },
     *  requirements={
     *  },
     * parameters={
     *    {"name"="url", "dataType"="string", "required"=false, "description"="Url of image", "default" = ""},
     * }
     * )
     */
    public function createScaledPhotosAction(Request $request)
    {
        /* Get manager */
        $em = $this->getDoctrine()->getManager();



        $url = $request->get('url');
//        var_dump($url);
//        die();

        /* Get Validator */
        $uploader = $this->container->get('services.image_uploader');

        $result = $uploader->createScaledPhotos( 'test' . rand(0, 100), 'test' , 'jpg', $url );

        $response = array(
            'message' => "Report is successfully liked!",
        );

        return new SCJsonResponse($result);
    }

    /**
     * @Rest\View
     * @ApiDoc(
     *  description="Like the report.",
     *  statusCodes = {
     *     200 = "Returned when successful",
     *   },
     *  requirements={
     *  },
     * parameters={
     *    {"name"="image", "dataType"="file", "required"=false, "description"="Image", "default" = ""}
     * }
     * )
     */
    public function uploadScaledPhotoAction(Request $request)
    {
        /* Get manager */
        $em = $this->getDoctrine()->getManager();

        $image = $request->files->get('image');

        /* Get Validator */
        $uploader = $this->container->get('services.image_uploader');

        $url = $uploader->upload($image, 'test', rand(1,1000));


        $response = array(
            'message' => "Report is successfully liked!",
        );

        return new SCJsonResponse($url);
    }


}