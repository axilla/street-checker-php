<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Impression;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * Created by PhpStorm.
 * User: axe
 * Date: 25.9.16.
 * Time: 21.30
 */
class ImpressionAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('owner')
                   ->add('report')
                   ->add('type', 'choice', [
                       'choices' => array(
                           'Like'    => Impression::SC_LIKE,
                           'Dislike' => Impression::SC_DISLIKE,
                           'Neutral' => Impression::SC_NEUTRAL,
                       )
                   ]);;

    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('owner')
                       ->add('report');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('id')
                   ->add('owner')
                   ->add('report')
                   ->add('type', 'choice', [
                       'choices' => array(
                           Impression::SC_LIKE    => "Like",
                           Impression::SC_DISLIKE => 'Dislike',
                           Impression::SC_NEUTRAL => 'Neutral'
                       )
                   ]);
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper->add('owner')
                   ->add('report');
    }
}