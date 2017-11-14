<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Created by PhpStorm.
 * User: axe
 * Date: 27.11.16.
 * Time: 16.45
 */
class AuthorizationService
{

    protected $em;
    protected $container;

    /* Type of Data */
    const SC_OWNER = 'owner';
    const SC_OPEN  = 'open';

    /* Array with type of Data */
    public static $SC_GRANTS = array(
        self::SC_OWNER,
        self::SC_OPEN
    );

    /**
     * Container is injected as service argument from configuration
     */
    public function __construct(Container $container, EntityManager $em)
    {

        $this->container = $container;
        $this->em = $em;
    }

    /**
     * Get Logged User and check if he has a grant for this action
     * @param Request $request
     * @param $entity
     * @param $grant
     * @return mixed
     */
    public function getLoggedUserAndSetGrant(Request $request, $entity, $grant)
    {
        $loggedUser = $this->getLoggedUser($request);

        if (!in_array($grant, self::$SC_GRANTS)) {
            throw new HttpException(500, 'Wrong type of grant!');
        }

        switch ($grant) {
            case self::SC_OWNER:
                $this->ownerGranted($entity, $loggedUser->getId());
                break;
            default:
                break;
        }
        return $loggedUser;
    }

    /**
     * Return logged user by sc-access-token from the header of the request
     * @param Request $request
     * @return $loggedUser
     */
    public function getLoggedUser(Request $request, $throwsException = true)
    {
        $accToken = $request->headers->get('sc-access-token');
        $loggedUser = $this->em->getRepository('AppBundle:User')->findOneByScAccessToken($accToken);
        if ($throwsException && !$loggedUser) {
            throw new HttpException(400, 'Logged user not found!');
        }

        return $loggedUser;
    }

    /**
     * Check if logged user is owner of the $entity
     * @param $entity
     * @param $loggedUserId
     */
    public function ownerGranted($entity, $loggedUserId)
    {
        /* Check if @entity has getOwner method defined */
        if (!method_exists($entity, 'getOwner')) {
            throw new HttpException(500, 'Method getOwner does not exist!');
        }
        /* Check if owner is defined */
        $owner = $entity->getOwner();
        $loggedUser = $this->em->getRepository('AppBundle:User')->find($loggedUserId);

        if (!$loggedUser->isAdmin()) {
            if (!$owner) {
                throw new HttpException(400, 'This object does not have an owner defined!');
            }

            /* Check if logged user is authorized for current action */
            if ($owner->getId() !== $loggedUserId) {
                throw new HttpException(403, 'You are not authorized for this action!');
            }
        }
    }
}