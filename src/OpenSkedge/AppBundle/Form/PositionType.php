<?php

namespace OpenSkedge\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PositionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('description', 'textarea', array('required' => false))
            ->add('area', 'entity', array(
                    'class' => 'OpenSkedgeBundle:Area',
                    'property' => 'name',
                    'multiple' => false))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OpenSkedge\AppBundle\Entity\Position'
        ));
    }

    public function getName()
    {
        return 'position';
    }
}
