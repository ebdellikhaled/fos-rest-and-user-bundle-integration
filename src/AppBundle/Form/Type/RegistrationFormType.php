<?php

// src/AppBundle/Form/RegistrationFormType.php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormBuilder;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('firstname');
        $builder->add('lastname');
        $builder->add('phone');
        $builder->add('department');
        $builder->add('role');
        $builder->add('projects');
        $builder->add('skills');

        // add your custom field
        //$builder->add('fname');
    }

    public function getFname()
    {
        return 'app_user_registration';
    }
}