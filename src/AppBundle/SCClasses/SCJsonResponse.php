<?php
namespace AppBundle\SCClasses;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Created by PhpStorm.
 * User: axe
 * Date: 5.11.16.
 * Time: 22.17
 */
class SCJsonResponse extends JsonResponse
{
    public function __construct($data = null, $status = 200, $headers = array(), $json = false)
    {
        if($status >= 400){
            $error = new SCJsonResponseError($data, $status);
            $data = $error->toArray();
        }elseif(is_string($data)){
            $data = array("message" => $data);
        }

        parent::__construct($data, $status, $headers, $json);
    }

    public function setError($message, $statusCode, $file = NULL, $trace = NULL)
    {
        $error = new SCJsonResponseError($message, $statusCode, $file, $trace);
        $this->setData($error->toArray());
    }
}