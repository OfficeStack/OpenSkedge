<?php

namespace OpenSkedge\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AvailabilityScheduleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('schedulePeriod', 'entity', array(
                  'label' => 'Schedule Period',
                  'class' => 'OpenSkedgeBundle:SchedulePeriod',
                  'empty_value' => 'Choose a Schedule Period')
            )
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OpenSkedge\AppBundle\Entity\AvailabilitySchedule'
        ));
    }

    public function getName()
    {
        return 'schedulePeriod';
    }
}
