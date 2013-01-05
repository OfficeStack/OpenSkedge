<?php

namespace FlexSched\SchedBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use FlexSched\SchedBundle\Entity\UserRepository;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', 'text')
                ->add('password', 'repeated', array(
                    'required' => false,
                    'first_name' => 'password',
                    'second_name' => 'confirm',
                    'type' => 'password'))
                ->add('name', 'text')
                ->add('workphone', 'text', array('required' => false))
                ->add('homephone', 'text', array('required' => false))
                ->add('location', 'text', array('required' => false))
                ->add('email', 'email')
                ->add('min', 'integer')
                ->add('max', 'integer')
                ->add('hours', 'integer', array('label' => 'Desired Hours', 'required' => false))
                ->add('notes', 'textarea', array('required' => false))
                ->add('supnotes', 'textarea', array('label' => 'Supervisor Notes', 'required' => false))
                ->add('color', 'text', array('required' => false))
                ->add('isActive', 'checkbox', array('label' => 'Active', 'required' => false))
                ->add('group', 'entity', array(
                        'label' => 'User Role',
                        'class' => 'FlexSchedBundle:Group',
                        'property' => 'name',
                        'multiple' => false))
                ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FlexSched\SchedBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'user';
    }
}
