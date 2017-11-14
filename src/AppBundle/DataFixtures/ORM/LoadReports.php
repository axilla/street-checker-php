<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Comment;
use AppBundle\Entity\CommentImage;
use AppBundle\Entity\Impression;
use AppBundle\Entity\ReportGrade;
use AppBundle\Entity\ReportImage;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Faker;

use AppBundle\Entity\Report;

class LoadReports extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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
        $faker = $faker = Faker\Factory::create();

        $usersCount = count($manager->getRepository('AppBundle:User')->findAll()) - 2;
        $categoriesCount = count($manager->getRepository('AppBundle:ReportCategory')->findAll()) - 1;
        for ($i = 1; $i < 1000; $i++) {

            $description = $this->container->get('helpers.text_generator')->getParagraph(rand(0, 100));
            $title = $this->container->get('helpers.text_generator')->getTitle(rand(0, 100));
            $address = $this->container->get('helpers.text_generator')->getTitle(rand(0, 100));

            $lat = rand(43343436, 43307300) / 1000000;
            $lng = rand(21867439, 21947825) / 1000000;

            $owner = $this->getReference('user-' . rand(1, $usersCount));

            $category = $this->getReference('report-category-' . rand(0, $categoriesCount));
            $categoryId = $category->getId();

            $reportTmp = new Report();
            $reportTmp->setTitle($title);
            $reportTmp->setDescription($description);
            $reportTmp->setLat($lat);
            $reportTmp->setLng($lng);
            $reportTmp->setAddress($address);
            $reportTmp->setOwner($owner);
            $reportTmp->setCategory($category);
            $reportTmp->setLevel(rand(1, 10));
            $reportTmp->setCreationTime($faker->dateTimeBetween('-60 days', 'now'));
            $reportTmp->setUrgency(1, 10);

            // Raport Images
            $imgCount = rand(0,4);
            for($imgIndex = 0; $imgIndex <= $imgCount; $imgIndex++){
                $img = new ReportImage();
                $imgUrl = 'http://street-checker.dev/images/' . $category->getId() .'/img-' .rand(1,15) . '.jpg';
                $imgThumbs = array($imgUrl, $imgUrl,$imgUrl);
                $img->setFullUrl($imgUrl);
                $img->setThumbsUrls($imgThumbs);
                $img->setReport($reportTmp);
                $manager->persist($img);
            }

            $commentMax = rand(0, 20);
            for ($j = 0; $j <= $commentMax; $j++) {

                $commentOwner = $this->getReference('user-' . rand(1, $usersCount));
                $commentText = $this->container->get('helpers.text_generator')->getParagraph(rand(1, 100));

                $commentTmp = new Comment($commentText, $reportTmp, $commentOwner);
                // Comment Images
                $k = rand(1,5);
                if($k > 3){
                    $img = new CommentImage();
                    $imgUrl = 'http://street-checker.dev/images/' . $category->getId() .'/img-' .rand(1,15) . '.jpg';
                    $imgThumbs = array($imgUrl, $imgUrl,$imgUrl);
                    $img->setFullUrl($imgUrl);
                    $img->setThumbsUrls($imgThumbs);
                    $img->setComment($commentTmp);
                    $manager->persist($img);
                }
                $manager->persist($commentTmp);
            }

            $gradeMax = rand(0, 10);
            for ($j = 0; $j <= $gradeMax; $j++) {

                $gradeOwner = $this->getReference('user-' . rand(1, $usersCount));
                $grade = rand(1, 10);

                $gradeTmp = new ReportGrade();
                $gradeTmp->setReport($reportTmp);
                $gradeTmp->setOwner($gradeOwner);
                $gradeTmp->setGrade($grade);
                $manager->persist($gradeTmp);
            }

            $manager->persist($reportTmp);
            $manager->flush();

            $this->addReference('report-' . $i, $reportTmp);
            $manager->persist($reportTmp);
        }

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 3; // the order in which fixtures will be loaded
    }

}
