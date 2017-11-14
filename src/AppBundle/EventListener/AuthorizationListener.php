<?php

namespace AppBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\ParameterBag;

use Symfony\Component\EventDispatcher\GenericEvent;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;



class AuthorizationListener
{
    /**
     * Container
     * @var object 
     */
    protected $container;
    protected $em;

    public function __construct(EntityManager $em, Container $container)
    {
        $this->em = $em;        
        $this->container = $container;        
    }
    
    
    public function onKernelRequest(GetResponseEvent $event)
    {
        
        $response = new Response();
        
        $request = $event->getRequest();
        $header = $request->headers;
        $requestParameters = $request->request;         // Request parameters 
        $requestAttributes = $request->attributes;      // Request attributes (Controller, Route ... )
        $route = $requestAttributes->get('_route');
        
        // Get open Routes defined in config.yaml
        $closedRoutes = $this->container->getParameter('sc.closed_routes');

        $root = $header->get('root');
        $rootApiPass = $this->container->getParameter('sc.root_api_pass');
        if(!$root || $root != $rootApiPass){
            if(in_array($route, $closedRoutes))
            {
                $now = new \DateTime();
                $now = $now->getTimestamp();
                // Try to get id and token from header
                $scToken = $header->get('sc-access-token');
                if(!$scToken ){
                    $scToken = $requestParameters->get('sc-access-token');
                }
                //Check paremethers
                if($scToken){
                    // Get User
                    $user = $this->em->getRepository('AppBundle:User')->findOneBy(array('scAccessToken' => $scToken));
                    // Check if user exist
                    if(!$user){
                        throw new HttpException(401, 'Wrong scAccessToken!');
                    }else{
                        $locked = $user->getLocked();
                        /* If user account is locked we checking how long it should stay locked */
                        if($locked){
                            $lastLocked = $user->getLastLocking()->getTimestamp();
                            $lockingExpiration = $this->container->getParameter('sc.locking.duration');
                            $lockingExpirationTime = strtotime($lockingExpiration, $lastLocked);

                            // Check if account is still locked
                            if( $now < $lockingExpirationTime){
                                throw new HttpException(401, 'Your account is temporary locked!');
                            }else{
                                $user->unlock();
                                $this->em->persist($user);
                                $this->em->flush();
                            }
                        }
                        //Check if user is already logged out
                        $isLoggedIn = $user->getIsLoggedIn();
                        if($isLoggedIn){
//********************   TOKEN EXPIRATION  ********************************/ 
                            // Get last logged in time
                            $lastLogin = $user->getLastLogin()->getTimestamp();
                            $tokenExpiration = $this->container->getParameter('sc.token.duration');
                            $expirationTime = strtotime($tokenExpiration, $lastLogin);

                            // Check if token is still expired
                            if( $now > $expirationTime){
                                $response->setStatusCode(401)
                                    ->setContent('Token expired, please login again.');
                            }
//********************   TOKEN EXPIRATION   END ********************************/ 

                        }else{
                            throw new HttpException(401, 'User is logged out!');
                        }
                    }

                }else{
                    throw new HttpException(401, 'Authentication parameters missing!');

                }

                if($response->getStatusCode() == 401){
                    $event->setResponse($response);
                }

            }else{
                
            }
            
        }
        
    }
    
}