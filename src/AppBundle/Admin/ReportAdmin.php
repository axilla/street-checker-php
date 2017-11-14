<?php

namespace AppBundle\Admin;

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
class ReportAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('title', 'text')
                   ->add('description', 'text')
                   ->add('owner')
                   ->add('category')
                   ->add('lat', 'number', array('scale' => 5))
                   ->add('lng', 'number', array('scale' => 5))
                   ->add('address', 'text')
                   ->add('level', 'choice', ['choices' => array_combine(range(1, 10), range(1, 10))])
                   ->add('urgency', 'number');
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('title')
                       ->add('description');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('id')
                   ->add('title')
                   ->add('description')
                   ->add('active', 'boolean')
                   ->add('report', \AppBundle\Entity\Report::class)
                   ->add('solution', \AppBundle\Entity\Report::class);
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper->add('title', 'text');
    }
}