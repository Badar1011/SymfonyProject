<?php
/**
 * Created by PhpStorm.
 * User: stc661
 * Date: 10/12/18
 * Time: 12:15
 */

namespace Book\ReviewBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseRegistrationFormType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('fullname');
        $builder->remove('username');
    }

    public function getParent()
    {
        return BaseRegistrationFormType::class;

    }




}