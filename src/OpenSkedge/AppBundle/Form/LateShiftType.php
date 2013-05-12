<?php

namespace OpenSkedge\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LateShiftType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $subscriber = new EventSubscriber\PatchSubscriber();
        $builder->addEventSubscriber($subscriber);
        $builder
            ->add('status')
            ->add('notes')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OpenSkedge\AppBundle\Entity\LateShift',
            'intention'  => 'lateshift_update'
        ));
    }

    public function getName()
    {
        return 'lateshift';
    }
}
