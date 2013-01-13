<?php

namespace FlexSched\SchedBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use FlexSched\SchedBundle\Entity\User;
use FlexSched\SchedBundle\Entity\Clock;
use FlexSched\SchedBundle\Form\UserType;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\Form;

/**
 * User controller.
 *
 */
class UserController extends Controller
{
    /**
     * Lists all User entities.
     *
     */
    public function indexAction()
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FlexSchedBundle:User')->findAll();

        return $this->render('FlexSchedBundle:User:index.html.twig', array(
            'userstitle' => 'Users',
            'entities' => $entities,
        ));
    }

    /**
     * Finds and displays a User entity.
     *
     */
    public function viewAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        if (is_null($id))
            $id = $this->getUser()->getId();
        $entity = $em->getRepository('FlexSchedBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        return $this->render('FlexSchedBundle:User:view.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a new User entity.
     *
     */
    public function createAction(Request $request)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $entity  = new User();
        $form = $this->createForm(new UserType(), $entity);

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $encoder = $this->get('security.encoder_factory')->getEncoder($entity);
                $password = $encoder->encodePassword($entity->getPassword(), $entity->getSalt());
                $entity->setPassword($password);
                $clock = new Clock();
                $em->persist($clock);
                $em->flush();
                $entity->setClock($clock);
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('user_view', array('id' => $entity->getId())));
            }
        }

        return $this->render('FlexSchedBundle:User:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Edits an existing User entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN') && $id != $this->getUser->getId()) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FlexSchedBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $originalPassword = $entity->getPassword();

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new UserType(), $entity);

        if ($request->getMethod() == 'POST') {
            $editForm->bind($request);
            if ($editForm->isValid()) {
                $this->cleanupCollections($editForm);
                $plainPassword = $editForm->get('password');
                if (!empty($plainPassword))  {
                    $encoder = $this->container->get('security.encoder_factory')->getEncoder($entity);
                    $password = $encoder->encodePassword($entity->getPassword(), $entity->getSalt());
                    $entity->setPassword($password);
                } else {
                    $entity->setPassword($originalPassword);
                }
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('user_view', array('id' => $id)));
            }
        }

        return $this->render('FlexSchedBundle:User:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a User entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN') || $id == $this->getUser->getId()) {
            throw new AccessDeniedException();
        }

        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FlexSchedBundle:User')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find User entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('user'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

    /**
     * Lists all supervisors for the User.
     */
    public function supervisorsAction()
    {
        $user = $this->getUser();

        $entities = $user->getSupervisors();

        return $this->render('FlexSchedBundle:User:index.html.twig', array(
            'displayonly' => true,
            'userstitle' => 'My Supervisors',
            'entities' => $entities,
        ));
    }

    /**
     * Lists all employees for the User.
     */
    public function employeesAction()
    {
        $user = $this->getUser();

        $entities = $user->getEmployees();

        return $this->render('FlexSchedBundle:User:index.html.twig', array(
            'displayonly' => true,
            'userstitle' => 'My Employees',
            'entities' => $entities,
        ));
    }

    public function colleaguesAction()
    {
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();

        $qb = $em->createQueryBuilder();

        $schedulePeriods = $qb->select('sp')
                              ->from('FlexSchedBundle:SchedulePeriod', 'sp')
                              ->where('sp.startTime < CURRENT_TIMESTAMP()')
                              ->andWhere('sp.endTime > CURRENT_TIMESTAMP()')
                              ->getQuery()
                              ->getResult();
        $schedules = array();

        $userPositions = array();

        foreach($schedulePeriods as $schedulePeriod) {
            try {
                $userPositions[] = $em->createQueryBuilder()
                                    ->select('p', 's')
                                    ->from('FlexSchedBundle:Schedule', 's')
                                    ->innerJoin('s.position', 'p', 'WITH', '(s.user = :uid AND s.schedulePeriod = :spid)')
                                    ->setParameters(array('uid' => $user->getId(), 'spid' => $schedulePeriod->getId()))
                                    ->getQuery()
                                    ->setMaxResults(1)
                                    ->getSingleResult()
                                    ->getPosition()
                                    ->getId();
            } catch (\Doctrine\ORM\NoResultException $e) {
                // It's aight.
            }
        }

        foreach($schedulePeriods as $schedulePeriod) {
            foreach($userPositions as $upid) {
                $schedules[] = $em->getRepository('FlexSchedBundle:Schedule')->findBy(array(
                    'schedulePeriod' => $schedulePeriod,
                    'position'       => $upid
                ));
            }
        }

        $entities = array();

        for ($i = 0; $i < count($schedules); $i++) {
            foreach($schedules[$i] as $schedule)
            {
                if ($schedule->getUser()->getId() != $user->getId())
                    $entities[] = $schedule->getUser();
            }
        }

        $entities = array_unique($entities);

        return $this->render('FlexSchedBundle:User:index.html.twig', array(
            'displayonly' => true,
            'userstitle' => 'My Colleagues',
            'entities' => $entities,
        ));
    }

    /**
     * Ensure that any removed items collections actually get removed
     *
     * @param \Symfony\Component\Form\Form $form
     */
    protected function cleanupCollections(Form $form)
    {
        $children = $form->getChildren();

        foreach ($children as $childForm) {
            $data = $childForm->getData();
            if ($data instanceof Collection) {

                // Get the child form objects and compare the data of each child against the object's current collection
                $proxies = $childForm->getChildren();
                foreach ($proxies as $proxy) {
                    $entity = $proxy->getData();
                    if (!$data->contains($entity)) {
                        // Entity has been removed from the collection
                        $em = $this->getDoctrine()->getEntityManager();
                        $em->remove($entity);
                    }
                }
            }
        }
    }
}
