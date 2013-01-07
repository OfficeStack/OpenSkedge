<?php

namespace FlexSched\SchedBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use FlexSched\SchedBundle\Entity\AvailabilitySchedule;
use FlexSched\SchedBundle\Form\AvailabilityScheduleType;

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
        $entities = $em->getRepository('FlexSchedBundle:AvailabilitySchedule')->findByUser($user);

        return $this->render('FlexSchedBundle:AvailabilitySchedule:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Finds and displays a AvailabilitySchedule entity.
     *
     */
    public function viewAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FlexSchedBundle:AvailabilitySchedule')->find($id);

        /*
         * TODO: Do not show times before or after certain points
         * htime by default should be set to mktime(0, 0, 0, 1, 1);
         * sectiondiv should be passed as the resolution (1 => 15min, 2 => 30min, 4 => 1hr)
         * sectionlen should be passed as (96/sectiondiv)
         * next_section should determine the next step up based on resolution "+15 minutes", "+30 minutes", "+1 hour"*/

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AvailabilitySchedule entity.');
        }

        $schedulePeriod = $entity->getSchedulePeriod();

        $scheduled = $em->getRepository('FlexSchedBundle:Schedule')->findBySchedulePeriod($schedulePeriod);

        $schedules = array();
        $positions = array();

        foreach($scheduled as $s)
        {
            $positions[] = $s->getPosition();
            $schedules[] = array(str_split($s->getSun()), str_split($s->getMon()), str_split($s->getTue()), str_split($s->getWed()), str_split($s->getThu()), str_split($s->getFri()), str_split($s->getSat()));
        }

        $week = array(str_split($entity->getSun()), str_split($entity->getMon()), str_split($entity->getTue()), str_split($entity->getWed()), str_split($entity->getThu()), str_split($entity->getFri()), str_split($entity->getSat()));

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FlexSchedBundle:AvailabilitySchedule:view.html.twig', array(
            'schedule_period' => $entity->getSchedulePeriod(),
            'week'      => $week,
            'htime'     => mktime(0,0,0,1,1),
            'resolution' => "1 hour",
            'schedules'   => $schedules,
            'positions'   => $positions,
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

        return $this->render('FlexSchedBundle:AvailabilitySchedule:precreate.html.twig', array(
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new AvailabilitySchedule entity.
     *
     */
    public function createAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $schedulePeriod = $em->getRepository('FlexSchedBundle:SchedulePeriod')->find($id);

        if (!$schedulePeriod) {
            throw $this->createNotFoundException('Unable to find SchedulePeriod entity.');
        }

        $entity  = new AvailabilitySchedule();

        $entity->setSchedulePeriod($schedulePeriod);

        $week = array(str_split($entity->getSun()), str_split($entity->getMon()), str_split($entity->getTue()), str_split($entity->getWed()), str_split($entity->getThu()), str_split($entity->getFri()), str_split($entity->getSat()));

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

        return $this->render('FlexSchedBundle:AvailabilitySchedule:new.html.twig', array(
            'schedule_period' => $schedulePeriod,
            'week'      => $week,
            'htime'     => mktime(0,0,0,1,1),
            'resolution' => "15 mins",
            'entity' => $entity,
            'create' => true,
        ));
    }

    /**
     * Edits an existing AvailabilitySchedule entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FlexSchedBundle:AvailabilitySchedule')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AvailabilitySchedule entity.');
        }

        $schedulePeriod = $entity->getSchedulePeriod();

        $scheduled = $em->getRepository('FlexSchedBundle:Schedule')->findBySchedulePeriod($schedulePeriod);

        $schedules = array();
        $positions = array();

        foreach($scheduled as $s)
        {
            $positions[] = $s->getPosition();
            $schedules[] = array(str_split($s->getSun()), str_split($s->getMon()), str_split($s->getTue()), str_split($s->getWed()), str_split($s->getThu()), str_split($s->getFri()), str_split($s->getSat()));
        }

        $week = array(str_split($entity->getSun()), str_split($entity->getMon()), str_split($entity->getTue()), str_split($entity->getWed()), str_split($entity->getThu()), str_split($entity->getFri()), str_split($entity->getSat()));

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

        return $this->render('FlexSchedBundle:AvailabilitySchedule:edit.html.twig', array(
            'schedule_period' => $entity->getSchedulePeriod(),
            'week'      => $week,
            'htime'     => mktime(0,0,0,1,1),
            'resolution' => '15 mins',
            'entity'      => $entity,
            'edit_form'   => true,
            'schedules'   => $schedules,
            'positions'   => $positions,
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
            $entity = $em->getRepository('FlexSchedBundle:AvailabilitySchedule')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AvailabilitySchedule entity.');
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
