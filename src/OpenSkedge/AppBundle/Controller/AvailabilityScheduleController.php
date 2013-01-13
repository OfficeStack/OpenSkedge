<?php

namespace OpenSkedge\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use OpenSkedge\AppBundle\Entity\AvailabilitySchedule;
use OpenSkedge\AppBundle\Form\AvailabilityScheduleType;

/**
 * AvailabilitySchedule controller.
 *
 */
class AvailabilityScheduleController extends Controller
{
    /**
     * Lists all AvailabilitySchedule entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();
        $entities = $em->getRepository('OpenSkedgeBundle:AvailabilitySchedule')->findByUser($user);

        return $this->render('OpenSkedgeBundle:AvailabilitySchedule:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Finds and displays a AvailabilitySchedule entity.
     *
     */
    public function viewAction($uid, $spid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OpenSkedgeBundle:AvailabilitySchedule')->findOneBy(array(
            'user' => $uid,
            'schedulePeriod' => $spid
        ));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AvailabilitySchedule entity.');
        }

        /*
         * TODO: Do not show times before or after certain points
         * htime by default should be set to mktime(0, 0, 0, 1, 1);
        */

        $schedules = $em->getRepository('OpenSkedgeBundle:Schedule')->findBy(array(
            'user' => $uid,
            'schedulePeriod' => $spid
        ));

        $deleteForm = $this->createDeleteForm($entity->getId());

        return $this->render('OpenSkedgeBundle:AvailabilitySchedule:view.html.twig', array(
            'htime'     => mktime(0,0,0,1,1),
            'resolution' => "1 hour",
            'avail'       => $entity,
            'schedules'   => $schedules,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    public function precreateAction(Request $request)
    {
        $form = $this->createForm(new AvailabilityScheduleType());

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $id = $form->getData()->getSchedulePeriod()->getId();
                return $this->redirect($this->generateUrl('user_schedule_new', array('id' => $id)));
            }
        }

        return $this->render('OpenSkedgeBundle:AvailabilitySchedule:precreate.html.twig', array(
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new AvailabilitySchedule entity.
     *
     */
    public function newAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $schedulePeriod = $em->getRepository('OpenSkedgeBundle:SchedulePeriod')->find($id);

        if (!$schedulePeriod) {
            throw $this->createNotFoundException('Unable to find SchedulePeriod entity.');
        }

        $user = $this->getUser();

        $entity = new AvailabilitySchedule();

        $entity->setSchedulePeriod($schedulePeriod);
        $entity->setUser($user);

        if ($request->getMethod() == 'POST') {
            $data = $request->request->get('day');
            $entity->setSun($data[0]);
            $entity->setMon($data[1]);
            $entity->setTue($data[2]);
            $entity->setWed($data[3]);
            $entity->setThu($data[4]);
            $entity->setFri($data[5]);
            $entity->setSat($data[6]);
            $entity->setLastUpdated();

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('user_schedule_view', array('id' => $entity->getId())));
        }

        return $this->render('OpenSkedgeBundle:AvailabilitySchedule:new.html.twig', array(
            'avail'      => $entity,
            'htime'      => mktime(0,0,0,1,1),
            'resolution' => "1 hour",
            'create'     => true,
        ));
    }

    /**
     * Edits an existing AvailabilitySchedule entity.
     *
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OpenSkedgeBundle:AvailabilitySchedule')->find($id);
        $user = $this->getUser();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AvailabilitySchedule entity.');
        }

        if ($user != $entity->getUser()) {
            throw new AccessDeniedException();
        }

        $schedulePeriod = $entity->getSchedulePeriod();

        $schedules = $em->getRepository('OpenSkedgeBundle:Schedule')->findBy(array('schedulePeriod' => $schedulePeriod->getId(), 'user' => $user->getId()));

        $deleteForm = $this->createDeleteForm($id);

        if ($request->getMethod() == 'POST') {
            $data = $request->request->get('day');
            $entity->setSun($data[0]);
            $entity->setMon($data[1]);
            $entity->setTue($data[2]);
            $entity->setWed($data[3]);
            $entity->setThu($data[4]);
            $entity->setFri($data[5]);
            $entity->setSat($data[6]);
            $entity->setLastUpdated(time());

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('user_schedule_edit', array('id' => $id)));
        }

        return $this->render('OpenSkedgeBundle:AvailabilitySchedule:edit.html.twig', array(
            'htime'       => mktime(0,0,0,1,1),
            'resolution'  => '1 hour',
            'avail'      => $entity,
            'edit'   => true,
            'schedules'   => $schedules,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a AvailabilitySchedule entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('OpenSkedgeBundle:AvailabilitySchedule')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AvailabilitySchedule entity.');
            }

            if ($this->getUser() != $entity->getUser()) {
                throw new AccessDeniedException();
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('user_availability'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
