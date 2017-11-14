<?php
/**
 * Created by PhpStorm.
 * User: axe
 * Date: 5.12.16.
 * Time: 19.50
 */

namespace AppBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class ImageUploaderService
{
    private $container;
    private $cdnUrl;

    /**
     * Container is injected as service argument from configuration
     */
    public function __construct(Container $container)
    {

        $this->container = $container;
        $this->cdnUrl = $this->container->getParameter('sc.cdn.local');
    }

    /**
     * Handles file upload
     * @param File|UploadedFile $file file to upload
     * @param string $folder folder name in which we are storing the file (t,l,a...)
     * @param int $id id of entity used for file/folder name
     * @return array Array with name folder extension etc
     */
    public function upload($file = null, $folder = 't', $id = 1)
    {
        $folder = "$folder" . $this->getFileDirectory($id);
        if ($file) {
            $name = sha1(time() . $file->getClientOriginalName());
            $extension = $file->guessExtension();
            $file->move($this->getUploadRootDir() . $folder, "$name.$extension");
        } else {
            return [
                'status'    => 1,
                'name'      => 'avatar-default',
                'folder'    => 'avatars',
                'extension' => 'png',
                'fullUrl'   => $this->container->getParameter('sc.avatar_default'),
                'cdn'       => 'none'
            ];
        }
        $fullUrl = "$this->cdnUrl/uploads/$folder/$name.$extension";  //add site prefix here

        return array('status'  => 1, 'name' => $name, 'extension' => $extension, 'folder' => $folder,
                     'fullUrl' => $fullUrl, 'cdn' => 'none');

    }

    /**
     * Create Scaled images from URL or File by dimensions from configuration
     *
     * @param $name - name of image
     * @param $folder - folder to store
     * @param $extension - extension of image
     * @param null $fullUrl - URL of image for scaling
     * @param null $originalPhoto - File for scaling
     * @return array|bool - Return array of thumbs URLs
     */
    public function createScaledPhotos($name, $folder, $extension, $fullUrl = NULL, $originalPhoto = NULL)
    {
        /*
         * Check if we get URL or Photo File
         */
        if ($fullUrl) {
            $image = file_get_contents($fullUrl);
            $originalImage = imagecreatefromstring($image);
        } elseif ($originalPhoto) {
            $originalImage = imagecreatefromstring($originalPhoto);
        } else {
            return FALSE;
        }
        // Get dimensions from config
        $dimensions = $this->container->getParameter('sc.photo_dimensions');
        // Get dimensions of original image
        $originalWidth = imagesx($originalImage);
        $originalHeight = imagesy($originalImage);
        $ratio = $originalWidth / $originalHeight;

        /*
         * We get array of dimensions of thumb images from configuration
         * Create scaled images for each thumb
         * Return URL of all thumbs
         */
        $urls = [];
        foreach ($dimensions as $key => $imgDim) {
            $destination = $this->getUploadRootDir() . $folder . '/' . $name . '_' . $key . '.' . $extension;
            $height = $imgDim['width'] / $ratio;
            $tmpImage = imagecreatetruecolor($imgDim['width'], $height);
            imagecopyresampled($tmpImage, $originalImage, 0, 0, 0, 0, $imgDim['width'], $height, $originalWidth, $originalHeight);

            $urls[] = $this->cdnUrl . "/uploads/" . $folder . '/' . $name . '_' . $key . '.' . $extension;

            /* Check image extension and save image */
            switch ($extension) {
                case 'gif':
                    imagegif($tmpImage, $destination);
                    break;
                case 'jpg':
                case 'jpeg':
                    imagejpeg($tmpImage, $destination, 100);
                    break;
                case 'png':
                    imagepng($tmpImage, $destination);
                    break;
                case 'bmp':
                    iamgewbmp($tmpImage, $destination);
                    break;
            }
        }

        return array('thumb_urls' => $urls);
    }

    /**
     * Upload base64 encoded image to our local storage
     * @param string $image Base64 encoded image
     * @param string $type Type of image (gif|jpg|jpeg|png|bmp)
     * @param string $folder Folder name if which we are storing the file
     * @param int $id id of entity used for file/folder name
     * @return array Array with name, folder, extension and etc.
     */
    public function uploadEncodedImage($image, $type, $folder = 'test', $id = 1)
    {
        /* Convert from base64 string */
        $base64_str = substr($image, strpos($image, ",") + 1);
        $data = base64_decode($base64_str);
        $originalImage = imagecreatefromstring($data);

        /* Create dir and name */
        $folder = $folder . $this->getFileDirectory($id);
        $extension = $type;
        $name = sha1(time() . rand(0, 10124));

        $destination = $this->getUploadRootDir() . $folder . '/' . $name . '.' . $extension;
        $fullUrl = "$this->cdnUrl/uploads/$folder/$name.$extension";  //add site prefix here

        /* Create folders for storing image */
        if (!file_exists($this->getUploadRootDir() . $folder)) {
            $this->makeDirs($this->getUploadRootDir() . $folder);
        }

        /* Check image extension and save image */
        switch ($type) {
            case 'gif':
                imagegif($originalImage, $destination);
                break;
            case 'jpg':
            case 'jpeg':
                imagejpeg($originalImage, $destination, 100);
                break;
            case 'png':
                imagepng($originalImage, $destination);
                break;
            case 'bmp':
                imagewbmp($originalImage, $destination);
                break;
        }

        /* Return informaions about image */
        return array('status' => 1, 'name' => $name, 'extension' => $extension, 'folder' => $folder, 'fullUrl' => $fullUrl, 'cdn' => $this->cdnUrl);
    }

    /**
     * This function create thumb images from base64 string
     * I created this function but I never use it
     * @param string $image base64 encoded image
     * @param string $type
     * @param string $folder
     * @param int $id
     * @param string $imageName
     * @return boolean
     */
    public function createThumbFromEncodedImage($image, $type, $folder = 'test', $id = 1, $imageName)
    {
        /* Convert from base64 string */
        $data = base64_decode($image);
        $originalImage = imagecreatefromstring($data);

        /* Create dir and name */
        $folder = $folder . $this->getFileDirectory($id);
        $extension = $type;
        $thumbName = $imageName . '_thmb';

        $thumbDestination = $this->getUploadRootDir() . $folder . '/' . $thumbName . '.' . $extension;

        /* Create folders for storing image */
        if (!file_exists($this->getUploadRootDir() . $folder)) {
            $this->makeDirs($this->getUploadRootDir() . $folder);
        }

        $thumbImage = imagecreatetruecolor(50, 50);
        imagecopyresampled($thumbImage, $originalImage, 0, 0, 0, 0, 50, 50, imagesx($originalImage), imagesy($originalImage));

        /* Check image extension and save image */
        switch ($type) {
            case 'gif':
                imagegif($thumbImage, $thumbDestination);
                break;
            case 'jpg':
            case 'jpeg':
                imagejpeg($thumbImage, $thumbDestination, 100);
                break;
            case 'png':
                imagepng($thumbImage, $thumbDestination);
                break;
            case 'bmp':
                imagewbmp($thumbImage, $thumbDestination);
                break;
        }

        /* Return informaions about image */
        return true;
    }

    /**
     * Make folder recursive
     * @param string $folder
     * @return boolean
     */
    private function makeDirs($folder)
    {
        if (!file_exists($folder)) {
            return mkdir($folder, 0777, true);
        }
        return false;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__ . '/../../../web/uploads/';
    }

    /**
     * Based on id creates folder structure
     * comprised of 1000 dirs, and 100 files in each third-level directory. This supports ~ 100 billion files.
     */
    protected function getFileDirectory($id)
    {

        $level1 = ($id / 100000000) % 100000000;
        $level2 = (($id - $level1 * 100000000) / 100000) % 100000;
        $level3 = (($id - ($level1 * 100000000) - ($level2 * 100000)) / 100) % 1000;
        $file = $id - (($level1 * 100000000) + ($level2 * 100000) + ($level3 * 100));

        return '/' . sprintf("%03d", $level1)
            . '/' . sprintf("%03d", $level2)
            . '/' . sprintf("%03d", $level3)
            . '/' . $file;
    }
}