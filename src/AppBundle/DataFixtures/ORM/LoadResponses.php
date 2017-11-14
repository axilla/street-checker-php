<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Impression;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use AppBundle\Entity\Report;
use AppBundle\Entity\ReportImage;

class LoadResponses extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {

        $usersCount = count($manager->getRepository('AppBundle:User')->findAll()) - 2;
        $reportsCount = count($manager->getRepository('AppBundle:Report')->findBy(array('report'=>NULL))) - 1;
        for ( $i = 1; $i < 1500; $i++ ){

            $description = $this->container->get('helpers.text_generator')->getParagraph(rand(0, 100) );
            $title = $this->container->get('helpers.text_generator')->getTitle(rand(0, 100) );
            $owner = $this->getReference('user-' . rand(1,$usersCount));

            $report = $this->getReference('report-' . rand(1,$reportsCount));
            $category = $report->getCategory();
            $categoryId = $category->getId();

            $responseTmp = new Report();
            $responseTmp->setTitle($title);
            $responseTmp->setDescription($description);
            $responseTmp->setLat($report->getLat());
            $responseTmp->setLng($report->getLng());
            $responseTmp->setAddress($report->getAddress());
            $responseTmp->setOwner($owner);
            $responseTmp->setCategory($report->getCategory());
            $responseTmp->setLevel(rand(1,10));
            $responseTmp->setUrgency(1, 10);
            $responseTmp->setReport($report);

            // Raport Images
            $imgCount = rand(0,4);
            for($imgIndex = 0; $imgIndex <= $imgCount; $imgIndex++){
                $img = new ReportImage();
                $imgUrl = 'http://street-checker.dev/images/solutions/' . $category->getId() .'/img-' .rand(1,10) . '.jpg';
                $imgThumbs = array($imgUrl, $imgUrl,$imgUrl);
                $img->setFullUrl($imgUrl);
                $img->setThumbsUrls($imgThumbs);
                $img->setReport($responseTmp);
                $manager->persist($img);
            }

            $impMax = rand(3, 10);
            for($j = 0; $j <= $impMax; $j++){

                $impOwner = $this->getReference('user-' . rand(1,$usersCount));
                $k = rand(1,5);
                if($k < 4) {
                    $impType = 1;
                }else{
                    $impType = 2;
                }

                $impTmp = new Impression($impType, $responseTmp, $impOwner);
                $manager->persist($impTmp);
            }

            $manager->persist($responseTmp);
            $manager->flush();

            $this->addReference('response-' . $i, $responseTmp);
            $manager->persist($report);
            $manager->persist($responseTmp);
        }

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 4; // the order in which fixtures will be loaded
    }

}
