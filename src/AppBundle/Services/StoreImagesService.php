<?php
/**
 * Created by PhpStorm.
 * User: axe
 * Date: 5.12.16.
 * Time: 19.48
 */

namespace AppBundle\Services;

use AppBundle\Entity\CommentImage;
use AppBundle\Entity\ProfileImage;
use AppBundle\Entity\ReportImage;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\HttpException;

class StoreImagesService
{
    protected $em;
    protected $container;

    /**
     * Container is injected as service argument from configuration
     */
    public function __construct(Container $container, EntityManager $em)
    {

        $this->container = $container;
        $this->em = $em;
    }

    /**
     * Store Binary Image on server
     * Set that image as ReportImage
     * Create Scaled (Thumbs)Images
     * @param $image - base64 string
     * @param $type - type of image ( jpg, jpeg, png, gif ...)
     * @param $reportId
     * @return ReportImage|bool
     */
    public function storeReportBinaryImage($image, $type, $reportId)
    {

        //Check if report exist
        $report = $this->em->getRepository('AppBundle:Report')->find($reportId);
        if (!$report) {
            throw new HttpException(404, 'Report not found');
        }
        /* Get upload manager */
        $uplManager = $this->container->get('services.image_uploader');
        /* Try to upload image */
        $result = $uplManager->uploadEncodedImage($image, $type, 'ri', $reportId);
        if ($result['status']) {
            /* Creating Thumbs images */
            $thumbResult = $uplManager->createScaledPhotos($result['name'], $result['folder'], $result['extension'], $result['fullUrl']);
            if (!$thumbResult) {
                throw new HttpException(400, 'Error durring creation of scaled images!');
            }

            /* Create new metoo photo */
            $reportImage = new ReportImage();
            $reportImage->setName($result['name']);
            $reportImage->setReport($report);
            $reportImage->setFolder($result['folder']);
            $reportImage->setExtension($result['extension']);
            $reportImage->setFullUrl($result['fullUrl']);
            $reportImage->setCdn($result['cdn']);
            $reportImage->setThumbsUrls($thumbResult);

            $this->em->persist($reportImage);
        }

        $this->em->flush();
        return $reportImage;
    }

    /**
     * Store Binary Image on server
     * Set that image as CommentImage
     * Create Scaled (Thumbs)Images
     * @param $image - base64 string
     * @param $type - type of image ( jpg, jpeg, png, gif ...)
     * @param $commentId
     * @return CommentImage|bool
     */
    public function storeCommentBinaryImage($image, $type, $commentId)
    {

        //Check if comment exist
        $comment = $this->em->getRepository('AppBundle:Comment')->find($commentId);
        if (!$comment) {
            throw new HttpException(404, 'Comment not found');
        }
        /* Get upload manager */
        $uplManager = $this->container->get('services.image_uploader');
        /* Try to upload image */
        $result = $uplManager->uploadEncodedImage($image, $type, 'ci', $commentId);
        if ($result['status']) {
            /* Creating Thumbs images */
            $thumbResult = $uplManager->createScaledPhotos($result['name'], $result['folder'], $result['extension'], $result['fullUrl']);
            if (!$thumbResult) {
                throw new HttpException(400, 'Error durring creation of scaled images!');
            }

            /* Create new metoo photo */
            $commentImage = new CommentImage();
            $commentImage->setName($result['name']);
            $commentImage->setComment($comment);
            $commentImage->setFolder($result['folder']);
            $commentImage->setExtension($result['extension']);
            $commentImage->setFullUrl($result['fullUrl']);
            $commentImage->setCdn($result['cdn']);
            $commentImage->setThumbsUrls($thumbResult);

            $this->em->persist($commentImage);
        }

        $this->em->flush();
        return $commentImage;
    }

    /**
     * Store Binary Image on server
     * Set that image as UserImage
     * Create Scaled (Thumbs)Images
     * @param $image - base64 string
     * @param $type - type of image ( jpg, jpeg, png, gif ...)
     * @param $userId
     * @return UserImage|bool
     */
    public function storeProfileBinaryImage($image, $type, $userId)
    {

        //Check if user exist
        $user = $this->em->getRepository('AppBundle:User')->find($userId);
        if (!$user) {
            throw new HttpException(404, 'User not found');
        }
        /* Get upload manager */
        $uplManager = $this->container->get('services.image_uploader');
        /* Try to upload image */
        $result = $uplManager->uploadEncodedImage($image, $type, 'pi', $userId);
        if ($result['status']) {
            /* Creating Thumbs images */
            $thumbResult = $uplManager->createScaledPhotos($result['name'], $result['folder'], $result['extension'], $result['fullUrl']);
            if (!$thumbResult) {
                throw new HttpException(400, 'Error durring creation of scaled images!');
            }

            /* Create new metoo photo */
            $userImage = new ProfileImage();
            $userImage->setName($result['name']);
            $userImage->setUser($user);
            $userImage->setFolder($result['folder']);
            $userImage->setExtension($result['extension']);
            $userImage->setFullUrl($result['fullUrl']);
            $userImage->setCdn($result['cdn']);
            $userImage->setThumbsUrls($thumbResult);

            $this->em->persist($userImage);
        }

        $this->em->flush();
        return $userImage;
    }

    /**
     * This method store report image to the DB
     * @param $image - file
     * @param $reportId
     * @return ReportImage - object
     */
    public function storeReportImage($image, $reportId)
    {

        //Check if report exist
        $report = $this->em->getRepository('AppBundle:Report')->find($reportId);
        if (!$report) {
            throw new HttpException(404, 'Report not found');
        }
        /* Get upload manager */
        $uplManager = $this->container->get('services.image_uploader');
        /* Try to upload image */
        $result = $uplManager->upload($image, 'ri', $reportId);
        if ($result['status']) {
            /* Creating Thumbs images */
            $thumbResult = $uplManager->createScaledPhotos($result['name'], $result['folder'], $result['extension'], $result['fullUrl']);
            if (!$thumbResult) {
                throw new HttpException(400, 'Error durring creation of scaled images!');
            }

            /* Create new metoo photo */
            $reportImage = new ReportImage();
            $reportImage->setName($result['name']);
            $reportImage->setReport($report);
            $reportImage->setFolder($result['folder']);
            $reportImage->setExtension($result['extension']);
            $reportImage->setFullUrl($result['fullUrl']);
            $reportImage->setCdn($result['cdn']);
            $reportImage->setThumbsUrls($thumbResult);

            $this->em->persist($reportImage);
        }

        $this->em->flush();
        return $reportImage;
    }

    /**
     */
    public function storeCommentImage($image, $commentId)
    {

        //Check if comment exist
        $comment = $this->em->getRepository('AppBundle:Comment')->find($commentId);
        if (!$comment) {
            throw new HttpException(404, 'Comment not found');
        }
        /* Get upload manager */
        $uplManager = $this->container->get('services.image_uploader');
        /* Try to upload image */
        $result = $uplManager->upload($image, 'ci', $commentId);
        if ($result['status']) {
            /* Creating Thumbs images */
            $thumbResult = $uplManager->createScaledPhotos($result['name'], $result['folder'], $result['extension'], $result['fullUrl']);
            if (!$thumbResult) {
                throw new HttpException(400, 'Error during creating scaled images!');
            }

            /* Create new metoo photo */
            $commentImage = new CommentImage();
            $commentImage->setName($result['name']);
            $commentImage->setComment($comment);
            $commentImage->setFolder($result['folder']);
            $commentImage->setExtension($result['extension']);
            $commentImage->setFullUrl($result['fullUrl']);
            $commentImage->setCdn($result['cdn']);
            $commentImage->setThumbsUrls($thumbResult);

            $this->em->persist($commentImage);
        }

        $this->em->flush();
        return $commentImage;
    }

    /**
     * @param null|UploadedFile $image
     * @param $userId
     * @return ProfileImage
     */
    public function storeProfileImage($image = null, $userId)
    {
        //Check if user exist\
        $cdnUrl = $this->container->getParameter('sc.cdn.local');
        $user = $this->em->getRepository('AppBundle:User')->find($userId);
        if (!$user) {
            throw new HttpException(404, 'User not found');
        }
        /* Get upload manager */
        $uplManager = $this->container->get('services.image_uploader');
        /* Try to upload image */
        $result = $uplManager->upload($image, 'pi', $userId);
        if ($result['status']) {
            if ($image) {
                /* Creating Thumbs images */
                $thumbResult = $uplManager->createScaledPhotos($result['name'], $result['folder'], $result['extension'], $result['fullUrl']);
                if (!$thumbResult) {
                    throw new HttpException(400, 'Error during creating scaled images!');
                }
            } else {
                $thumbResult = [
                    $this->container->getParameter('sc.avatar_large'),
                    $this->container->getParameter('sc.avatar_medium'),
                    $this->container->getParameter('sc.avatar_small'),
                ];
            }

            /* Create new metoo photo */
            $userImage = new ProfileImage();
            $userImage->setName($result['name']);
            $userImage->setUser($user);
            $userImage->setFolder($result['folder']);
            $userImage->setExtension($result['extension']);
            $userImage->setFullUrl($result['fullUrl']);
            $userImage->setCdn($result['cdn']);
            $userImage->setThumbsUrls($thumbResult);

            $this->em->persist($userImage);
        }

        $this->em->flush();
        return $userImage;
    }

    private function parseBase64File($file)
    {
        $ext = substr($file, strpos($file, '/') + 1, 3);
        $base64_str = substr($file, strpos($file, ",") + 1);
        return [
            'extension' => $ext,
            'data'      => $base64_str
        ];
    }
}