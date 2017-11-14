<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\ReportCategory;

class LoadReportCategories extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {

        $categoryTitles = [
            'Noise',
            'Stray Dogs',
            'Garbage',
            'Fire',
        ];

        foreach ($categoryTitles as $key => $categoryTitle) {
            $categoryTmp = new ReportCategory();
            $categoryTmp->setTitle($categoryTitle);
            $this->addReference('report-category-' . $key, $categoryTmp);
            $manager->persist($categoryTmp);
        }

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2; // the order in which fixtures will be loaded
    }

}
