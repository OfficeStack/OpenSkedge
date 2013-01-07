<?php

namespace FlexSched\SchedBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SchedulePeriodType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startTime', 'date', array(
                  'widget' => 'single_text',
                  'label' => 'Start Date',
                  'format' => 'MM-dd-yyyy',
                  'empty_value' => 'mm-dd-yyyy',
                  'attr' => array('class'=>'datepicker'))
                 )
            ->add('endTime', 'date', array(
                  'widget' => 'single_text',
                  'label' => 'End Date',
                  'format' => 'MM-dd-yyyy',
                  'empty_value' => 'mm-dd-yyyy',
                  'attr' => array('class'=>'datepicker'))
                 )
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FlexSched\SchedBundle\Entity\SchedulePeriod'
        ));
    }

    public function getName()
    {
        return 'flexsched_schedbundle_scheduleperiodtype';
    }
}
