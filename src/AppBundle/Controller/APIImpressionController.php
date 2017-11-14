<?php

namespace AppBundle\Controller;

/**
 * Created by PhpStorm.
 * User: axe
 * Date: 27.11.16.
 * Time: 18.56
 */

use AppBundle\Entity\Impression;
use AppBundle\SCClasses\SCJsonResponse;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

# important for documentation, must have
#in case that u need view object!
# must have for rest anotations

class APIImpressionController extends FOSRestController
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
        $responseId = $validator->check($request->get('reportId'), 'integer', 'reportId', true, false);
        $response = $em->getRepository('AppBundle:Report')->find($responseId);
        if (!$response) {
            throw  new HttpException(404, "Report not found!");
        }

        if ($response->getOwner()->getId() == $loggedUser->getId()) {
            throw  new HttpException(403, "You cannot like or dislike your own response");
        }


        $like = $em->getRepository('AppBundle:Impression')->findOneBy(array('report' => $responseId, 'owner' => $loggedUser));

        if ($like) {
            $like->like();

        } else {
            $like = new Impression(Impression::SC_LIKE, $response, $loggedUser);

        }
        $em->persist($like);
        $em->flush();
        $em->refresh($response->getReport());

        //resolve if the conditions for automatic resolving of the report are matched
        $reportService = $this->container->get('services.report_validation');
        $isResolved = false;
        if ($reportService->shouldCheckForAutomaticResolve($like)) {
            $isResolved = $reportService->resolveReport($like->getReport());
        }
        $em->flush();

        $serializer = $this->container->get('jms_serializer');
        $like = json_decode($serializer->serialize($like, 'json'));

        $response = array(
            'message'          => !$isResolved ? "Report is successfully liked!" : "Report is successfully liked and resolved!",
            'comment'          => $like,
            'isReportResolved' => $isResolved
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
        $response = $em->getRepository('AppBundle:Report')->find($reportId);
        if (!$response) {
            throw  new HttpException(404, "Report not found!");
        }

        if ($response->getOwner()->getId() == $loggedUser->getId()) {
            throw  new HttpException(403, "You cannot like or dislike your own report");
        }

        $dislike = $em->getRepository('AppBundle:Impression')->findOneBy(array('report' => $reportId, 'owner' => $loggedUser));
        if ($dislike) {
            $dislike->dislike();
        } else {
            $dislike = new Impression(Impression::SC_DISLIKE, $response, $loggedUser);
        }

        $em->persist($dislike);
        $em->flush();
        $em->refresh($response->getReport());

        //resolve if the conditions for automatic resolving of the report are matched
        $reportService = $this->container->get('services.report_validation');
        $isResolved = false;
        if ($reportService->shouldCheckForAutomaticResolve($dislike)) {
            $isResolved = $reportService->resolveReport($dislike->getReport());
        }
        $em->flush();

        $serializer = $this->container->get('jms_serializer');
        $dislike = json_decode($serializer->serialize($dislike, 'json'));

        $response = array(
            'message' => !$isResolved ? "Report is successfully disliked!" : "Report is successfully disliked and resolved!",
            'comment' => $dislike
        );

        return new SCJsonResponse($response);
    }


}