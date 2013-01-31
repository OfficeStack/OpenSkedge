<?php

namespace OpenSkedge\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use OpenSkedge\AppBundle\Entity\AvailabilitySchedule;
use OpenSkedge\AppBundle\Form\AvailabilityScheduleType;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\ArrayAdapter;

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
    public function indexAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        if(is_null($id)) {
            $user = $this->getUser();
            $title = 'My Schedules';
        } else {
            $user = $em->getRepository('OpenSkedgeBundle:User')->find($id);

            if (!$user) {
                throw $this->createNotFoundException('Unable to find User');
            }
            $title = $user->getName()."'s Schedules";
        }

        $availSchedules = $em->getRepository('OpenSkedgeBundle:AvailabilitySchedule')->findByUser($user);

        $page = $this->container->get('request')->query->get('page', 1);

        $adapter = new ArrayAdapter($availSchedules);
        $paginator = new Pagerfanta($adapter);
        $paginator->setMaxPerPage(15);
        $paginator->setCurrentPage($page);

        $entities = $paginator->getCurrentPageResults();

        return $this->render('OpenSkedgeBundle:AvailabilitySchedule:index.html.twig', array(
            'title'     => $title,
            'user'      => $user,
            'entities'  => $entities,
            'paginator' => $paginator,
        ));
    }

    /**
     * Finds and displays a AvailabilitySchedule entity.
     *
     */
    public function viewAction(Request $request, $uid, $spid)
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

        $resolution = $request->request->get('timeresolution', '1 hour');

        $deleteForm = $this->createDeleteForm($uid, $spid);

        return $this->render('OpenSkedgeBundle:AvailabilitySchedule:view.html.twig', array(
            'htime'       => mktime(0,0,0,1,1),
            'resolution'  => $resolution,
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
                $spid = $form->getData()->getSchedulePeriod()->getId();
                return $this->redirect($this->generateUrl('user_schedule_new', array('spid' => $spid)));
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
    public function newAction(Request $request, $spid)
    {
        $em = $this->getDoctrine()->getManager();

        $schedulePeriod = $em->getRepository('OpenSkedgeBundle:SchedulePeriod')->find($spid);

        if (!$schedulePeriod) {
            throw $this->createNotFoundException('Unable to find SchedulePeriod entity.');
        }

        $user = $this->getUser();

        $existing = $em->getRepository('OpenSkedgeBundle:AvailabilitySchedule')->findBy(array(
            'user' => $user->getId(),
            'schedulePeriod' => $spid
        ));

        if(!empty($existing)) {
            return $this->render('OpenSkedgeBundle::error.html.twig', array(
                'action' => 'New Availability Schedule',
                'error' => array(
                    'title' => 'Availability Schedule Already Exists',
                    'msg' => 'You already have an availability schedule for the schedule period you selected.'
                    )
            ));
        }

        $resolution = $request->query->get('timeresolution', '1 hour');

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

            return $this->redirect($this->generateUrl('user_schedule_view', array(
                'uid'  => $user->getId(),
                'spid' => $spid
            )));
        }

        return $this->render('OpenSkedgeBundle:AvailabilitySchedule:new.html.twig', array(
            'avail'      => $entity,
            'htime'      => mktime(0,0,0,1,1),
            'resolution' => $resolution,
            'create'     => true,
        ));
    }

    /**
     * Edits an existing AvailabilitySchedule entity.
     *
     */
    public function editAction(Request $request, $spid)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        $entity = $em->getRepository('OpenSkedgeBundle:AvailabilitySchedule')->findOneBy(array(
            'user' => $user->getId(),
            'schedulePeriod' => $spid
        ));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AvailabilitySchedule entity.');
        }

        if ($user != $entity->getUser()) {
            throw new AccessDeniedException();
        }

        $resolution = $request->query->get('timeresolution', '1 hour');

        $schedules = $em->getRepository('OpenSkedgeBundle:Schedule')->findBy(array('schedulePeriod' => $spid, 'user' => $user->getId()));

        $deleteForm = $this->createDeleteForm($user->getId(), $spid);

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

            return $this->redirect($this->generateUrl('user_schedule_view', array(
                'user'=> $user->getId(),
                'spid' => $spid
            )));
        }

        return $this->render('OpenSkedgeBundle:AvailabilitySchedule:edit.html.twig', array(
            'htime'       => mktime(0,0,0,1,1),
            'resolution'  => $resolution,
            'avail'       => $entity,
            'edit'        => true,
            'schedules'   => $schedules,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a AvailabilitySchedule entity.
     *
     */
    public function deleteAction(Request $request, $uid, $spid)
    {
        $form = $this->createDeleteForm($uid, $spid);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('OpenSkedgeBundle:AvailabilitySchedule')->findOneBy();

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

    private function createDeleteForm($uid, $spid)
    {
        return $this->createFormBuilder(array('uid' => $uid, 'spid' => $spid))
            ->add('uid', 'hidden')
            ->add('spid', 'hidden')
            ->getForm()
        ;
    }
}
