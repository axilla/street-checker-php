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

class APIImpressionController  extends FOSRestController
{

    /**
     * @Rest\View
     * @ApiDoc(
     *  description="Like the report.",
     *  statusCodes = {
     *     200 = "Returned when successful",
     *   },
     *  requirements={
     *      {"name"="reportId", "dataType"="integer", "requirement"="\d+", "description"="Id of the Report"},
     *  },
     * parameters={
     * }
     * )
     */
    public function likeAction(Request $request)
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

        $like = $em->getRepository('AppBundle:Impression')->findOneBy(array('report' => $reportId, 'owner' => $loggedUser ));
        if($like){
            $like->like();
        }else{
            $like = new Impression(Impression::SC_LIKE, $report, $loggedUser);

        }

        $em->persist($like);
        $em->flush();


        $serializer = $this->container->get('jms_serializer');
        $like = json_decode($serializer->serialize($like, 'json'));

        $response = array(
            'message' => "Report is successfully liked!",
            'comment' => $like
        );

        return new SCJsonResponse($response);
    }

    /**
     * @Rest\View
     * @ApiDoc(
     *  description="Dislike a report.",
     *  statusCodes = {
     *     200 = "Returned when successful",
     *   },
     *  requirements={
     *      {"name"="reportId", "dataType"="integer", "requirement"="\d+", "description"="Id of the Report"},
     *  },
     * parameters={
     * }
     * )
     */
    public function dislikeAction(Request $request)
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

        $dislike = $em->getRepository('AppBundle:Impression')->findOneBy(array('report' => $reportId, 'owner' => $loggedUser ));
        if($dislike){
            $dislike->dislike();
        }else{
            $dislike = new Impression(Impression::SC_DISLIKE, $report, $loggedUser);
        }

        $em->persist($dislike);
        $em->flush();


        $serializer = $this->container->get('jms_serializer');
        $dislike = json_decode($serializer->serialize($dislike, 'json'));

        $response = array(
            'message' => "Report is successfully disliked!",
            'comment' => $dislike
        );

        return new SCJsonResponse($response);
    }


}