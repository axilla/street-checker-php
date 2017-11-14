<?php

namespace AppBundle\Controller;

use AppBundle\SCClasses\SCJsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExceptionController extends Controller
{
    /**
     * This controller is triggered when Exception is thrown
     * @param Request $request
     * @return SCJsonResponse
     */
    public function showAction(Request $request)
    {
        // catch the exception
        $exception = $request->get('exception');
        if(method_exists($exception, 'getStatusCode')){
            $statusCode = $exception->getStatusCode();
//        }elseif(method_exists($exception, 'getCode')){
//            $statusCode = $exception->getCode();
        }else{
            $statusCode = 500;
        }

        if(method_exists($exception, 'getMessage')){
            $message = $exception->getMessage();
        }else{
            $class = get_class($exception);
            $message = "Unexpected exception has been detected ( $class )";
        }

        $env =  $this->container->get( 'kernel' )->getEnvironment();

        /* Create response from SCJsonResponse */
        $response = new SCJsonResponse($message, $statusCode);
        /* If development environment is detected, we throw more info about error */
        if($env == 'dev'){
            $file = $exception->getFile();
            $trace = $exception->getTrace();
            $response->setError($message, $statusCode, $file, $trace);
        }
        return $response;
    }
}
