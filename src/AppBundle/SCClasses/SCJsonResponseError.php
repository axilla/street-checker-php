<?php
namespace AppBundle\SCClasses;

/**
 * Created by PhpStorm.
 * User: axe
 * Date: 6.11.16.
 * Time: 17.06
 */
class SCJsonResponseError
{
    private $message;
    private $statusCode;
    private $file;
    private $trace;

    function __construct($message = NULL, $statusCode = NULL, $file = NULL, $trace = NULL)
    {
        $this->message = $message;
        $this->statusCode = $statusCode;
        $this->file = $file;
        $this->trace = $trace;
    }

    /**
     * @return array
     */
    public function toArray(){

        if($this->getMessage()){
            $ErArray['message'] = $this->getMessage();
        }
        if($this->getStatusCode()){
            $ErArray['statusCode'] = $this->getStatusCode();
        }
        if($this->getFile()){
            $ErArray['file'] = $this->getFile();
        }
        if($this->getTrace()){
            $ErArray['trace'] = $this->getTrace();
        }
        return $ErArray;
    }

    /**
     * @return null
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param null $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return null
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param null $statusCode
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @return null
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param null $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return null
     */
    public function getTrace()
    {
        return $this->trace;
    }

    /**
     * @param null $trace
     */
    public function setTrace($trace)
    {
        $this->trace = $trace;
    }

}