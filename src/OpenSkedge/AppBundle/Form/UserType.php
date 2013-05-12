<?php

namespace OpenSkedge\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use OpenSkedge\AppBundle\Entity\UserRepository;

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
                ->add('workphone', 'text', array('required' => false, 'label' => 'Work Phone'))
                ->add('homephone', 'text', array('required' => false, 'label' => 'Home Phone'))
                ->add('location', 'text', array('required' => false))
                ->add('email', 'email')
                ->add('min', 'integer', array('label' => 'Minimum Hours'))
                ->add('max', 'integer', array('label' => 'Maximum Hours'))
                ->add('hours', 'integer', array('label' => 'Desired Hours'))
                ->add('notes', 'textarea', array('required' => false))
                ->add('supnotes', 'textarea', array('label' => 'Supervisor Notes', 'required' => false))
                ->add('color', 'text', array('required' => false, 'attr' => array('class' => 'span12')))
                ->add('isActive', 'checkbox', array('label' => 'Active', 'required' => false))
                ->add('group', 'entity', array(
                        'label' => 'User Role',
                        'class' => 'OpenSkedgeBundle:Group',
                        'property' => 'name',
                        'multiple' => false))
                ->add('supervisors', 'entity', array(
                        'attr' => array('class' => 'input-xlarge'),
                        'required' => false,
                        'label' => 'Supervisors',
                        'class' => 'OpenSkedgeBundle:User',
                        'property' => 'name',
                        'multiple' => true,
                        'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                            $qb = $er->createQueryBuilder('u');
                            $qb->select('u, g')
                               ->leftJoin('u.group', 'g')
                               ->where($qb->expr()->in('g.name', array('Supervisor', 'Admin')))
                               ->orderBy('u.name', 'DESC');
                            return $qb;
                        }
                      ))
                ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OpenSkedge\AppBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'user';
    }
}
