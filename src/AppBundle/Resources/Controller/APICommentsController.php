<?php
namespace AppBundle\Controller;
/**
 * Created by PhpStorm.
 * User: axe
 * Date: 27.11.16.
 * Time: 18.56
 */

use AppBundle\Entity\Comment;
use AppBundle\Entity\Report;
use AppBundle\SCClasses\SCJsonResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc; # important for documentation, must have
use FOS\RestBundle\View\View; #in case that u need view object!
use FOS\RestBundle\Controller\Annotations as Rest; # must have for rest anotations

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpKernel\Exception\HttpException;

class APICommentsController  extends FOSRestController
{

    /**
     * @Rest\View
     * @ApiDoc(
     *  description="Add comment on the report.",
     *  statusCodes = {
     *     200 = "Returned when successful",
     *   },
     *  requirements={
     *      {"name"="reportId", "dataType"="integer", "requirement"="\d+", "description"="Id of the Report"},
     *      {"name"="text", "dataType"="float", "requirement"="\f+", "description"="Text of the comment"},
     *  },
     * parameters={
     *      {"name"="CommentImages", "dataType"="file", "required"=false, "description"="Base64 encoded image", "default" = ""},
     *      {"name"="binaryCommentImage", "dataType"="string", "required"=false, "description"="Base64 encoded image", "default" = ""},
     *      {"name"="binaryCommentImageType", "dataType"="string", "required"=false, "description"="Image type(jpg/png/gif/jpeg/bmp)", "default" = ""}
     * }
     * )
     */
    public function createAction(Request $request)
    {
        /* Get manager */
        $em = $this->getDoctrine()->getManager();

        $auth = $this->container->get('services.auth_service');
        $loggedUser = $auth->getLoggedUser($request);

        /* Get Validator */
        $validator = $this->container->get('helpers.input_validator');
        $reportId = $validator->check($request->get('reportId'), 'integer', 'reportId', true, false);
        $report = $em->getRepository('AppBundle:Report')->find($reportId);
        if(!$report){
            throw  new HttpException(404, "Report not found!");
        }

        $text = $validator->check($request->get('text'), 'string', 'text', true, false);
        $newComment = new Comment($text, $report, $loggedUser);
        $em->persist($newComment);
        $em->flush();

        $binaryCoverImage = $request->get('binaryCommentImage');
        $binaryCoverImageType = $request->get('binaryCommentImageType');
        /* If we have binary image upload them */
        if ($binaryCoverImage || $binaryCoverImageType) {

            $binaryPhotosTypes = $this->container->getParameter('sc.images.binary.types');
            if (!in_array($binaryCoverImageType, $binaryPhotosTypes)) {
                throw new HttpException(400, 'Invalid type of binary photo!');
            }

            $storePhotos = $this->get('services.store_images');
            $storePhotos->storeCommentBinaryImage($binaryCoverImage, $binaryCoverImageType, $newComment->getId());
        }

        $images = $request->files->get('CommentImages');
        if($images){
            $storePhotos = $this->get('services.store_images');
            if(!is_array($images)){
                $images = array($images);
            }
            foreach ($images as $image){
                $storePhotos->storeCommentImage($image, $newComment->getId());
            }
        }


        $em->refresh($newComment);
        $serializer = $this->container->get('jms_serializer');
        $comment = json_decode($serializer->serialize($newComment, 'json'));

        $response = array(
            'message' => "Comment successifully added.",
            'comment' => $comment
        );

        return new SCJsonResponse($response);
    }


    /**
     * @Rest\View
     * @ApiDoc(
     *  description="Add comment on the report.",
     *  statusCodes = {
     *     200 = "Returned when successful",
     *   },
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="Id of the Comment"},
     *  },
     * parameters={
     * }
     * )
     */
    public function deleteAction(Request $request)
    {
        /* Get manager */
        $em = $this->getDoctrine()->getManager();

        $auth = $this->container->get('services.auth_service');
        $loggedUser = $auth->getLoggedUser($request);

        /* Get Validator */
        $validator = $this->container->get('helpers.input_validator');
        $id = $validator->check($request->get('id'), 'integer', 'id', true, false);
        $comment = $em->getRepository('AppBundle:Comment')->find($id);
        if(!$comment){
            throw  new HttpException(404, "Comment not found!");
        }

        /* Check if logged user is authorized for this action */
        $auth = $this->container->get('services.auth_service');
        $loggedUser = $auth->getLoggedUserAndSetGrant($request, $comment, 'owner');

        $em->remove($comment);
        $em->flush();

        return new SCJsonResponse("Comment successifully removed.");
    }
}