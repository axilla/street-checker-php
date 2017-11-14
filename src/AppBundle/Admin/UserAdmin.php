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
class UserAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('firstName', 'text')
                   ->add('lastName', 'text')
                   ->add('username')
                   ->add('email', 'email')
                   ->add('password', 'password')
                   ->add('admin');
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('firstName')
                       ->add('lastName')
                       ->add('username');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('id')
                   ->add('firstName')
                   ->add('lastName')
                   ->add('admin');
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper->add('firstName', 'text')
                   ->add('lastName', 'text');
    }
}