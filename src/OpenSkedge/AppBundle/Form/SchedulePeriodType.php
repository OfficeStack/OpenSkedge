<?php

namespace OpenSkedge\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SchedulePeriodType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $today = new \DateTime('now');
        $builder
            ->add('startTime', 'date', array(
                  'widget' => 'single_text',
                  'label' => 'Start Date',
                  'format' => 'MM-dd-yyyy',
                  'empty_value' => $today->format('M-d-y'),
                 ))
            ->add('endTime', 'date', array(
                  'widget' => 'single_text',
                  'label' => 'End Date',
                  'format' => 'MM-dd-yyyy',
                  'empty_value' => 'mm-dd-yyyy',
                 ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OpenSkedge\AppBundle\Entity\SchedulePeriod'
        ));
    }

    public function getName()
    {
        return 'schedule_period';
    }
}
