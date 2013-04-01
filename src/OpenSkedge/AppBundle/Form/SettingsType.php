<?php

namespace OpenSkedge\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $days = array('sunday' => 'Sunday', 'monday' => 'Monday', 'tuesday' => 'Tuesday', 'wednesday' =>'Wednesday', 'thursday' => 'Thursday', 'friday' => 'Friday', 'saturday' => 'Saturday');
        $builder
            ->add('brandName', 'text', array('label' => 'Brand Name'))
            ->add('pruneAfter', 'integer', array('attr' => array('class' => 'span12'), 'label' => 'Keep time clock data for'))
            ->add('weekStartDay', 'choice', array('label' => 'Week Start Day', 'choices' => $days))
            ->add('weekStartDayClock', 'choice', array('label' => 'Week Start Day (Pay Period)','choices' => $days))
            ->add('defaultTimeResolution', 'choice', array(
                    'label' => 'Default Time Resolution',
                    'choices'   => array(
                        '15 mins' => '15 mins',
                        '30 mins' => '30 mins',
                        '1 hour'  => '1 hour',
                    ),
                ))
            ->add('startHour', 'text', array(
                    'label' => 'Schedule Start Hour',
                    'attr' => array('class' => 'span12', 'data-show-inputs' => false)
                ))
            ->add('endHour', 'text', array(
                    'label' => 'Schedule End Hour',
                    'attr' => array('class' => 'span12', 'data-show-inputs' => false)
                ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OpenSkedge\AppBundle\Entity\Settings'
        ));
    }

    public function getName()
    {
        return 'settings';
    }
}
