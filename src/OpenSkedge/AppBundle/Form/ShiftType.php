<?php

namespace OpenSkedge\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ShiftType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startTime', 'datetime', array(
                    'date_widget' => 'single_text',
                    'time_widget' => 'single_text',
                    'date_format' => 'yyyy-M-d',
                    'label' => 'Shift Start Time'
                ))
            ->add('endTime', 'datetime', array(
                    'date_widget' => 'single_text',
                    'time_widget' => 'single_text',
                    'date_format' => 'yyyy-M-d',
                    'label' => 'Shift End Time'
                ))
            ->add('status')
            ->add('notes', 'textarea', array(
                    'label' => 'Notes',
                    'attr'  => array(
                        'class' => 'input-xlarge',
                        'rows'  => 6
                    )
                ))
            ->add('pickedUpBy', 'entity', array(
                    'class' => 'OpenSkedgeBundle:User'
                ))
            ->add('schedulePeriod', 'entity', array(
                    'class' => 'OpenSkedgeBundle:SchedulePeriod',
                    'label' => 'Schedule Period',
                    'attr'  => array('class' => 'input-xlarge')
                ))
            ->add('position', 'entity', array(
                    'class' => 'OpenSkedgeBundle:Position',
                    'attr'  => array('class' => 'input-xlarge')
                ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OpenSkedge\AppBundle\Entity\Shift',
            'intention'  => 'shift_modify'
        ));
    }

    public function getName()
    {
        return 'shift';
    }
}
