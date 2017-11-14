<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\ProfileImage;
use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;


class LoadUsers extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{

    private $container;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $cdnUrl = $this->container->getParameter('sc.cdn.local');
        $thumbResult = [
            $this->container->getParameter('sc.avatar_large'),
            $this->container->getParameter('sc.avatar_medium'),
            $this->container->getParameter('sc.avatar_small'),
        ];

        $admin = new User();

        $admin->setUsername('admin')
              ->setEmail('admin@street-checker.dev')
              ->setAdmin(true)
              ->setFirstName('admin')
              ->setLastName('admin')
              ->setPassword('123')
              ->setGender('male');

        $this->addReference('admin', $admin);
        $manager->persist($admin);
        $manager->flush();

        //save profile image for admin
        $userImage = new ProfileImage();
        $userImage->setName('avatar-default');
        $userImage->setUser($admin);
        $userImage->setFolder('avatars');
        $userImage->setExtension('png');
        $imgUrl = 'http://street-checker.dev/images/profile_images/img-' .rand(1,12) . '.jpg';
        $userImage->setFullUrl($this->container->getParameter('sc.avatar_default'));
        $userImage->setCdn('none');
        $userImage->setThumbsUrls($thumbResult);

        $manager->persist($userImage);


        for ($i = 0; $i <= 20; $i++) {
            $user = new User();
            $user->setUsername("user" . $i)
                 ->setEmail("user" . $i . "@street-checker.dev")
                 ->setAdmin(false)
                 ->setFirstName("User_" . $i)
                 ->setLastName("UserLastName_" . $i)
                 ->setPassword('123')
                 ->setGender('male');

            $this->addReference('user-' . $i, $user);
            $manager->persist($user);

            //save profile images


            $userImage = new ProfileImage();
            $userImage->setName('avatar-default');
            $userImage->setUser($user);
            $userImage->setFolder('avatars');
            $userImage->setExtension('png');
            $imgUrl = 'http://street-checker.dev/images/profile_images/img-' .rand(1,13) . '.jpg';
            $userImage->setFullUrl($imgUrl);
            $userImage->setCdn('none');
            $thumbResult = [
                $imgUrl,
                $imgUrl,
                $imgUrl,
            ];
            $userImage->setThumbsUrls($thumbResult);

            $manager->persist($userImage);


        }
        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1; // the order in which fixtures will be loaded
    }

    /**
     * Sets the container.
     *
     * @param Container|null $container A ContainerInterface instance or null
     */
    public function setContainer(Container $container = null)
    {
        $this->container = $container;
    }
}
