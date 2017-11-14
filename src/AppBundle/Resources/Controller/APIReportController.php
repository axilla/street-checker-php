<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Report;
use AppBundle\SCClasses\SCJsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc; # important for documentation, must have
use FOS\RestBundle\View\View; #in case that u need view object!
use FOS\RestBundle\Controller\Annotations as Rest; # must have for rest anotations

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpKernel\Exception\HttpException;

class APIReportController extends FOSRestController
{
    /**
     * @Rest\View
     * @ApiDoc(
     *  description="Returns list of Reports by Filters",
     *  statusCodes = {
     *     200 = "Returned when successful",
     *     204 = "Returned when no results"
     *   },
     *  requirements = {
     *  },
     *  parameters={
     *          {"name"="reportId", "dataType"="integer", "required"=false, "description"="Id of parent Report", "default" = ""},
     *  },
     *  filters={
     *      {"name"="nwLat", "dataType"="float", "description"="North-West Latitude", "default" = "43.335274"},
     *      {"name"="nwLng", "dataType"="float", "description"="North-West Longitude", "default" = "21.866226"},
     *      {"name"="seLat", "dataType"="float", "description"="South-East Latitude", "default" = "43.301054"},
     *      {"name"="seLng", "dataType"="float", "description"="South-East Longitude", "default" = "21.950426"},
     *      {"name"="offset", "dataType"="integer", "description"="List offset"},
     *      {"name"="limit", "dataType"="integer", "description"="List limit"},
     *      {"name"="sortingColumn", "dataType"="string", "description"="Name of column by which to sort"},
     *      {"name"="sorting", "dataType"="string", "pattern"="ASC|DESC", "description"="List sorting direction"},
     *  }
     * )
     */
    public function listAction(Request $request)
    {
        /* Get manager */
        $em = $this->getDoctrine()->getManager();
        // Get filters from query string
        $query = $request->query;
        /* Get Validator */
        $validator = $this->container->get('helpers.input_validator');
        $nwLat = $validator->check($query->filter('nwLat'), 'float', 'nwLat', false);
        $nwLng = $validator->check($query->filter('nwLng'), 'float', 'nwLng', false);
        $seLat = $validator->check($query->filter('seLat'), 'float', 'seLat', false);
        $seLng = $validator->check($query->filter('seLng'), 'float', 'seLng', false);

        $reportId = $validator->check($request->get('reportId'), 'float', 'seLng', false);
        if ($reportId) {
            $report = $em->getRepository('AppBundle:Report')->find($reportId);
        } else {
            $report = NULL;
        }

        /* Check if ofsset or limit is set */
        $offset = $validator->check($query->filter('offset'), 'integer', 'offset', false);
        if (is_null($offset)) {
            $offset = 0;
        }
        $limit = $validator->check($query->filter('limit'), 'integer', 'limit', false);
        $sortingColumn = $validator->check($query->filter('sortingColumn'), 'string', 'sortingColumn', false);
        $sorting = $validator->check($query->filter('sorting'), 'string', 'sorting', false);
        // Get specific fields to show
        $fields = $this->container->getParameter('report.listObject');

        /* If Nort-West and South-East coordinates are sent we get Reports by Area */
        if ($nwLat and $nwLng and $seLat and $seLng) {
            /** Check if we valid coordinates */
            if ($nwLat >= $seLat and $nwLng <= $seLat) {
                $reports = $em->getRepository("AppBundle:Report")->getListByArea($fields, $reportId, $nwLat, $nwLng, $seLat, $seLng, $offset, $limit, $sortingColumn, $sorting);
            } else {
                throw new HttpException(400, "Invalid coordinates have been received. North-West point must be to the north and west of the second point.");
            }

        } else {
            $reports = $em->getRepository("AppBundle:Report")->getList($fields, $reportId, $offset, $limit, $sortingColumn, $sorting);
        }

        return new SCJsonResponse($reports);
    }

    /**
     * @Rest\View
     * @ApiDoc(
     *  description="Return Location object with comments, items prices, categories",
     *  statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when Location not found"
     *   },
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="Id of Report"}
     *  },
     *  filters={
     *      {"name"="offsetComments", "dataType"="integer", "description"="Comments offset"},
     *      {"name"="limitComments", "dataType"="integer", "description"="Comments limit"},
     *  }
     * )
     */
    public function showAction(Request $request)
    {
        /* Get manager */
        $em = $this->getDoctrine()->getManager();
        // Get filters from query string
        $query = $request->query;
        $id = $request->get('id');
        /* Get Validator */
        $validator = $this->container->get('helpers.input_validator');
        /* Check if ofsset or limit is set */
        $offsetComments = $validator->check($query->filter('offsetComments'), 'integer', 'offsetComments');
        if (is_null($offsetComments)) {
            $offsetComments = 0;
        }

        $report = $em->getRepository('AppBundle:Report')->find($id);

        if (!$report) {
            throw new HttpException(404, "Report with id = $id, has not been found!");
        }
        $serializer = $this->container->get('jms_serializer');
        $report = json_decode($serializer->serialize($report, 'json', Report::getSerializationContext()));

        return new SCJsonResponse($report);
    }

    /**
     * @Rest\View
     * @ApiDoc(
     *  description="Return Location object with comments, items prices, categories",
     *  statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when Location not found"
     *   },
     *  requirements={
     *      {"name"="title", "dataType"="integer", "requirement"="\d+", "description"="Title of the Report"},
     *      {"name"="lat", "dataType"="float", "requirement"="\f+", "description"="Latitude of the Report"},
     *      {"name"="lng", "dataType"="float", "requirement"="\f+", "description"="Longitude of the Report"},
     *      {"name"="category", "dataType"="integer", "requirement"="\d+", "description"="Category of the Report"},
     *  },
     * parameters={
     *          {"name"="reportId", "dataType"="string", "required"=false, "description"="Id of parent Report.", "default" = ""},
     *          {"name"="description", "dataType"="string", "required"=false, "description"="Description of the Report.", "default" = ""},
     *          {"name"="address", "dataType"="string", "required"=false, "description"="Address of the Report.", "default"=""},
     *          {"name"="level", "dataType"="integer", "required"=false, "description"="Level of the Report.", "default"=""},
     *          {"name"="urgency", "dataType"="integer", "required"=false, "description"="Level of Urgency of the Report.", "default"=""},
     *          {"name"="type", "dataType"="integer", "required"=false, "description"="Type of the Report", "default"=""},
     *          {"name"="ReportImages", "dataType"="file", "required"=false, "description"="Base64 encoded image", "default" = ""},
     *          {"name"="binaryReportImage", "dataType"="string", "required"=false, "description"="Base64 encoded image", "default" = ""},
     *          {"name"="binaryReportImageType", "dataType"="string", "required"=false, "description"="Image type(jpg/png/gif/jpeg/bmp)", "default" = ""}
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
        $newReport = new Report();

        $reportId = $validator->check($request->get('reportId'), 'integer', 'description');
        if ($reportId) {
            $report = $em->getRepository('AppBundle:Report')->find($reportId);
            if (!$report) {
                throw new HttpException(404, 'Report not found!');
            }
            $newReport->setReport($report);
            $em->persist($report);

            /* Set title */
            $title = $validator->check($request->get('title'), 'string', 'title');
            if ($title) {
                $newReport->setTitle($title);
            }
            /* Set $lat */
            $lat = $validator->check($request->get('lat'), 'float', 'lat');
            if ($lat) {
                $newReport->setLat($lat);
            }
            /* Set $lng */
            $lng = $validator->check($request->get('lng'), 'float', 'lng');
            if ($lng) {
                $newReport->setLng($lng);
            }

        } else {
            /* Set title */
            $title = $validator->check($request->get('title'), 'string', 'title', true, false);
            if ($title) {
                $newReport->setTitle($title);
            }
            /* Set $lat */
            $lat = $validator->check($request->get('lat'), 'float', 'lat', true, false);
            if ($lat) {
                $newReport->setLat($lat);
            }
            /* Set $lng */
            $lng = $validator->check($request->get('lng'), 'float', 'lng', true, false);
            if ($lng) {
                $newReport->setLng($lng);
            }

            /* Check if category exist */
            $categoryId = $validator->check($request->get('category'), 'integer', 'category');
            $category = $em->getRepository('AppBundle:ReportCategory')->find($categoryId);
            if ($category) {
                $newReport->setCategory($category);
            } else {
                throw new HttpException(400, 'Category is required field');
            }
        }

        $description = $validator->check($request->get('description'), 'string', 'description');
        if ($description) {
            $newReport->setDescription($description);
        }

        $address = $validator->check($request->get('address'), 'string', 'address');
        if ($address) {
            $newReport->setAddress($address);
        }

        $level = $validator->check($request->get('level'), 'integer', 'level');
        if ($level) {
            $newReport->setLevel($level);
        }

        $urgency = $validator->check($request->get('urgency'), 'integer', 'urgency');
        if ($urgency) {
            $newReport->setUrgency($urgency);
        }

        $type = $validator->check($request->get('type'), 'integer', 'type');
        if ($type) {
            $newReport->setType($type);
        }

        $newReport->setOwner($loggedUser);

        $em->persist($newReport);
        $em->flush();

        $binaryCoverImage = $request->get('binaryReportImage');
        $binaryCoverImageType = $request->get('binaryReportImageType');
        /* If we have binary cover image upload them */
        if ($binaryCoverImage || $binaryCoverImageType) {

            $binaryPhotosTypes = $this->container->getParameter('sc.images.binary.types');
            if (!in_array($binaryCoverImageType, $binaryPhotosTypes)) {
                throw new HttpException(400, 'Invalid type of binary photo!');
            }

            $storePhotos = $this->get('services.store_images');
            $storePhotos->storeReportBinaryImage($binaryCoverImage, $binaryCoverImageType, $newReport->getId());
        }

        $images = $request->files->get('ReportImages');
        if ($images) {
            $storePhotos = $this->get('services.store_images');
            if (!is_array($images)) {
                $images = array($images);
            }
            foreach ($images as $image) {
                $storePhotos->storeReportImage($image, $newReport->getId());
            }
        }

        $em->refresh($newReport);
        $serializer = $this->container->get('jms_serializer');
        $newReport = json_decode($serializer->serialize($newReport, 'json'));

        return new SCJsonResponse($newReport);
    }

    /**
     * @Rest\View
     * @ApiDoc(
     *  description="Return Location object with comments, items prices, categories",
     *  statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when Location not found"
     *   },
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="Id of Report"}
     *  },
     * parameters={
     *          {"name"="title", "dataType"="string", "required"=false, "description"="New Title.", "default" = ""},
     *          {"name"="description", "dataType"="string", "required"=false, "description"="New Description.", "default" = ""},
     *          {"name"="lat", "dataType"="float", "required"=false, "description"="New Latitude.", "default"=""},
     *          {"name"="lng", "dataType"="float", "required"=false, "description"="New Longitude.", "default"=""},
     *          {"name"="address", "dataType"="string", "required"=false, "description"="New Address.", "default"=""},
     *          {"name"="level", "dataType"="integer", "required"=false, "description"="New Level.", "default"=""},
     *          {"name"="urgency", "dataType"="integer", "required"=false, "description"="New Level of Urgency.", "default"=""},
     *          {"name"="type", "dataType"="integer", "required"=false, "description"="New Type.", "default"=""},
     * }
     * )
     */
    public function updateAction(Request $request)
    {
        /* Get manager */
        $em = $this->getDoctrine()->getManager();
        // Get filters from query string
        $id = $request->get('id');
        $report = $em->getRepository('AppBundle:Report')->findOneBy(array('id' => $id, 'active' => 1));
        if (!$report) {
            throw new HttpException(404, "Report with id = $id has not been found.");
        }

        /* Check if logged user is authorized for this action */
        $auth = $this->container->get('services.auth_service');
        $loggedUser = $auth->getLoggedUserAndSetGrant($request, $report, 'owner');

        /* Get Validator */
        $validator = $this->container->get('helpers.input_validator');

        /* Set title if new one is send */
        $title = $validator->check($request->get('title'), 'string', 'title');
        if ($title) {
            $report->setTitle($title);
        }
        /* Set $description if new one is send */
        $description = $validator->check($request->get('description'), 'string', 'description');
        if ($description) {
            $report->setDescription($description);
        }
        /* Set $address if new one is send */
        $address = $validator->check($request->get('address'), 'string', 'address');
        if ($address) {
            $report->setAddress($address);
        }
        /* Set $lat if new one is send */
        $lat = $validator->check($request->get('lat'), 'float', 'lat');
        if ($lat) {
            $report->setLat($lat);
        }
        /* Set $lng if new one is send */
        $lng = $validator->check($request->get('lng'), 'float', 'lng');
        if ($lng) {
            $report->setLng($lng);
        }
        /* Set $level if new one is send */
        $level = $validator->check($request->get('level'), 'integer', 'level');
        if ($level) {
            $report->setLevel($level);
        }
        /* Set $urgency if new one is send */
        $urgency = $validator->check($request->get('urgency'), 'integer', 'urgency');
        if ($urgency) {
            $report->setUrgency($urgency);
        }
        /* Set $type if new one is send */
        $type = $validator->check($request->get('type'), 'integer', 'type');
        if ($type) {
            $report->setType($type);
        }

        $em->persist($report);
        $em->flush();

        if (!$report) {
            throw new HttpException(404, "Report has not been found!");
        }
        $serializer = $this->container->get('jms_serializer');
        $report = json_decode($serializer->serialize($report, 'json'));

        return new SCJsonResponse($report);
    }

    /**
     * @Rest\View
     * @ApiDoc(
     *  description="Return Location object with comments, items prices, categories",
     *  statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when Location not found"
     *   },
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="Id of Report"}
     *  },
     * parameters={
     *          {"name"="ReportImages", "dataType"="file", "multiple"=true, "required"=false, "description"="New Title.", "default" = ""},
     * }
     * )
     */
    public function addImagesAction(Request $request)
    {
        /* Get manager */
        $em = $this->getDoctrine()->getManager();
        // Get filters from query string
        $query = $request->query;
        $id = $request->get('id');
        $report = $em->getRepository('AppBundle:Report')->findOneBy(array('id' => $id, 'active' => 1));
        if (!$report) {
            throw new HttpException(404, "Report with id = $id has not been found.");
        }

        /* Check if logged user is authorized for this action */
        $auth = $this->container->get('services.auth_service');
        $loggedUser = $auth->getLoggedUserAndSetGrant($request, $report, 'owner');

        $images = $request->files->get('ReportImages');
        if ($images) {
            $storePhotos = $this->get('services.store_images');
            if (!is_array($images)) {
                $images = array($images);
            }
            foreach ($images as $image) {
                $storePhotos->storeReportImage($image, $report->getId());
            }
        }

        $em->persist($report);
        $em->flush();

        if (!$report) {
            throw new HttpException(404, "Report has not been found!");
        }
        $serializer = $this->container->get('jms_serializer');
        $report = json_decode($serializer->serialize($report, 'json'));

        return new SCJsonResponse($report);
    }


    /**
     * @Rest\View
     * @ApiDoc(
     *  description="Remove ReportImage",
     *  statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when Location not found"
     *   },
     *  requirements={
     *      {"name"="imageId", "dataType"="integer", "requirement"="\d+", "description"="Id of ReportImage"}
     *  },
     *  filters={
     *  }
     * )
     */
    public function deleteImageAction(Request $request)
    {
        /* Get manager */
        $em = $this->getDoctrine()->getManager();
        // Get filters from query string
        $id = $request->get('imageId');
        $reportImage = $em->getRepository('AppBundle:ReportImage')->find($id);
        if (!$reportImage) {
            throw new HttpException(404, "ReportImage with id = $id, has not been found!");
        }

        /* Check if logged user is authorized for this action */
        $auth = $this->container->get('services.auth_service');
//        $loggedUser = $auth->getLoggedUserAndSetGrant($request, $reportImage, 'owner');

        $em->remove($reportImage);
        $em->flush();

        return new SCJsonResponse("Report Image is successfully deleted!");
    }

    /**
     * @Rest\View
     * @ApiDoc(
     *  description="Return Location object with comments, items prices, categories",
     *  statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when Location not found"
     *   },
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="Id of Report"}
     *  },
     *  filters={
     *  }
     * )
     */
    public function deleteAction(Request $request)
    {
        /* Get manager */
        $em = $this->getDoctrine()->getManager();
        // Get filters from query string
        $id = $request->get('id');
        $report = $em->getRepository('AppBundle:Report')->find($id);
        if (!$report) {
            throw new HttpException(404, "Report with id = $id, has not been found!");
        }

        /* Check if logged user is authorized for this action */
        $auth = $this->container->get('services.auth_service');
        $loggedUser = $auth->getLoggedUserAndSetGrant($request, $report, 'owner');

        /* Check if logged user is authorized for this action */
        $auth = $this->container->get('services.auth_service');
        $auth->ownerGranted($report, $loggedUser->getId());

        $em->remove($report);
        $em->flush();

        return new SCJsonResponse("Report is successfully deleted!");
    }
}
