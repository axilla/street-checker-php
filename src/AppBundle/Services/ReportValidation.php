<?php

namespace AppBundle\Services;

use AppBundle\Entity\Impression;
use AppBundle\Entity\Report;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

/**
 * Created by PhpStorm.
 * User: axe
 * Date: 27.11.16.
 * Time: 16.45
 */
class ReportValidation
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
     * @param Report $response
     * @return bool
     * @internal param $entity
     * @internal param $loggedUserId
     */
    public function canBeSolution(Report $response)
    {
        $numberOfLikesNeeded = $this->container->getParameter('sc.number_of_likes');
        $maxPercentageOfDislikes = $this->container->getParameter('sc.maximum_dislike_percentage');

        $numberOfResponseLikes = $numberOfResponseDislikes = 0;
        $response->getImpressions()->map(function (Impression $impression) use (&$numberOfResponseLikes, &$numberOfResponseDislikes) {
            if ($impression->getType() == Impression::SC_LIKE) {
                $numberOfResponseLikes++;
            } else if ($impression->getType() == Impression::SC_DISLIKE) {
                $numberOfResponseDislikes++;
            }
        });

        if ($numberOfResponseLikes >= $numberOfLikesNeeded) {
            if ($numberOfResponseDislikes > ($numberOfResponseLikes * $maxPercentageOfDislikes / 100)) {
                return false;
            }
        } else {
            return false;
        }
        return true;
    }

    /**
     * @param Impression $likeOrDislike
     * @return bool
     */
    public function shouldCheckForAutomaticResolve(Impression $likeOrDislike)
    {
        return $likeOrDislike->getType() == 0 || $likeOrDislike->getType() == Impression::SC_LIKE;
    }

    /**
     * @param Report $response
     * @param bool $isAutomatic
     * @return bool
     * @internal param Impression $impression
     */
    public function resolveReport(Report $response, $isAutomatic = false)
    {
        if ($isAutomatic || $this->canBeSolution($response)) {
            $originalReport = $response->getReport();
            $originalReport->setSolution($response);
            $originalReport->setActive('0');
            return true;
        }
        return false;
    }

}