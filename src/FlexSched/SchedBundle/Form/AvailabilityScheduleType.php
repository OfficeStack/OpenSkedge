<?php

namespace FlexSched\SchedBundle\Form;

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
                  'class' => 'FlexSchedBundle:SchedulePeriod',
                  'empty_value' => 'Choose a Schedule Period')
            )
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FlexSched\SchedBundle\Entity\AvailabilitySchedule'
        ));
    }

    public function getName()
    {
        return 'flexsched_schedbundle_availabilityscheduletype';
    }
}
