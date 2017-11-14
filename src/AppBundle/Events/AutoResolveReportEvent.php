<?php

namespace AppBundle\Events;

use AppBundle\Entity\Impression;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\EventDispatcher\Event;

class AutoResolveReportEvent extends Event
{
    protected $impression;
    protected $entityManager;

    public function __construct(Impression $impression, ObjectManager $entityManager)
    {
        $this->impression = $impression;
        $this->entityManager = $entityManager;
    }

    public function getImpression()
    {
        return $this->impression;
    }

    /**
     * @return ObjectManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }
}