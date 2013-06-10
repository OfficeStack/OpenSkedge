<?php

namespace OpenSkedge\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use OpenSkedge\AppBundle\Entity\AvailabilitySchedule;
use OpenSkedge\AppBundle\Entity\SchedulePeriod;
use OpenSkedge\AppBundle\Entity\User;
use OpenSkedge\AppBundle\Form\AvailabilityScheduleType;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\ArrayAdapter;

/**
 * Controller for CRUD operations on AvailabilitySchedule entities
 *
 * @category Controller
 * @package  OpenSkedge\AppBundle\Controller
 * @author   Max Fierke <max@maxfierke.com>
 * @license  GNU General Public License, version 3
 * @version  GIT: $Id$
 * @link     https://github.com/maxfierke/OpenSkedge OpenSkedge Github
 */
class AvailabilityScheduleController extends Controller
{
    /**
     * Lists all AvailabilitySchedule entities for the specified user id.
     *
     * @param integer $id ID of User entity
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        if (is_null($id)) {
            $user = $this->getUser();
            $title = 'My Schedules';
        } else {
            $user = $em->getRepository('OpenSkedgeBundle:User')->find($id);

            if (!$user instanceof User) {
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
     * Finds and displays a user's schedule for a specific schedule period.
     *
     * @param Request $request The user's request object
     * @param integer $uid     User ID from route
     * @param integer $spid    Schedule period ID from route
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(Request $request, $uid, $spid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OpenSkedgeBundle:AvailabilitySchedule')->findOneBy(array(
            'user' => $uid,
            'schedulePeriod' => $spid
        ));

        if (!$entity instanceof AvailabilitySchedule) {
            throw $this->createNotFoundException('Unable to find AvailabilitySchedule entity.');
        }

        $schedules = $em->getRepository('OpenSkedgeBundle:Schedule')->findBy(array(
            'user' => $uid,
            'schedulePeriod' => $spid
        ));

        $appSettings = $this->get('app_settings')->getAppSettings();

        $resolution = $request->request->get('timeresolution', $appSettings->getDefaultTimeResolution());

        $deleteForm = $this->createDeleteForm($uid, $spid);

        $dtUtils = $this->get('dt_utils');

        $startIndex = $dtUtils->getIndexFromTime($appSettings->getStartHour());
        $endIndex = $dtUtils->getIndexFromTime($appSettings->getEndHour())-1;

        $hoursAvailable = 0;
        for ($day = 0; $day < 7; $day++) {
            $timeRec = $entity->getDay($day);
            $hoursAvailable += substr_count($timeRec, '1') + substr_count($timeRec, '2') + substr_count($timeRec, '3');
        }
        $hoursAvailable = $hoursAvailable / 4;

        $hoursScheduled = 0;
        foreach($schedules as $schedule) {
            $scheduleSum = 0;
            for ($day = 0; $day < 7; $day++) {
                $scheduleSum += substr_count($schedule->getDay($day), '1');
            }
            $hoursScheduled += $scheduleSum / 4;
        }

        return $this->render('OpenSkedgeBundle:AvailabilitySchedule:view.html.twig', array(
            'htime'       => $dtUtils->timeStrToDateTime($appSettings->getStartHour()),
            'resolution'  => $resolution,
            'avail'       => $entity,
            'schedules'   => $schedules,
            'delete_form' => $deleteForm->createView(),
            'startIndex'  => $startIndex,
            'endIndex'    => $endIndex,
            'hrsAvail'    => $hoursAvailable,
            'hrsSched'    => $hoursScheduled
        ));
    }

    /**
     * Requests a schedule period to create an availablity schedule for
     * and passes it on to newAction()
     *
     * @param Request $request The user's request object
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function precreateAction(Request $request)
    {
        $form = $this->createForm(new AvailabilityScheduleType());

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $spid = $form->getData()->getSchedulePeriod()->getId();
                return $this->redirect($this->generateUrl('user_schedule_new', array('spid' => $spid)));
            }
            $request->getSession()->getFlashBag()->add('error', 'Availability schedule could not be created! Check for form errors below. If the issue persists, please report it to your friendly sysadmin.');
        }

        return $this->render('OpenSkedgeBundle:AvailabilitySchedule:precreate.html.twig', array(
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new AvailabilitySchedule entity.
     *
     * @param Request $request The user's request object
     * @param integer $spid    Schedule period ID from route
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request, $spid)
    {
        $em = $this->getDoctrine()->getManager();

        $schedulePeriod = $em->getRepository('OpenSkedgeBundle:SchedulePeriod')->find($spid);

        if (!$schedulePeriod instanceof SchedulePeriod) {
            throw $this->createNotFoundException('Unable to find SchedulePeriod entity.');
        }

        $user = $this->getUser();

        $existing = $em->getRepository('OpenSkedgeBundle:AvailabilitySchedule')->findBy(array(
            'user' => $user->getId(),
            'schedulePeriod' => $spid
        ));

        // If an availability schedule already exists for the given schedule period, give an error.
        if (!empty($existing)) {
            $request->getSession()->getFlashBag()->add('error', 'Availability schedule could not be created! You already have an availability schedule for the schedule period you selected.');
            return $this->redirect($this->generateUrl('user_schedules'));
        }

        $appSettings = $this->get('app_settings')->getAppSettings();

        $resolution = $request->query->get('timeresolution', $appSettings->getDefaultTimeResolution());

        $entity = new AvailabilitySchedule();

        $entity->setSchedulePeriod($schedulePeriod);
        $entity->setUser($user);

        if ($request->getMethod() == 'POST') {
            $data = $request->request->get('day');
            for ($i = 0; $i < 7; $i++) {
                $entity->setDay($i, $data[$i]);
            }
            $entity->setLastUpdated();

            $em->persist($entity);
            $em->flush();

            $mailer = $this->container->get('notify_mailer');
            $mailer->notifyAvailabilitySchedulePost($entity);

            return $this->redirect($this->generateUrl('user_schedule_view', array(
                'uid'  => $user->getId(),
                'spid' => $spid
            )));
        }

        $dtUtils = $this->get('dt_utils');

        $startIndex = $dtUtils->getIndexFromTime($appSettings->getStartHour());
        $endIndex = $dtUtils->getIndexFromTime($appSettings->getEndHour())-1;

        return $this->render('OpenSkedgeBundle:AvailabilitySchedule:new.html.twig', array(
            'avail'      => $entity,
            'htime'      => $dtUtils->timeStrToDateTime($appSettings->getStartHour()),
            'resolution' => $resolution,
            'create'     => true,
            'startIndex' => $startIndex,
            'endIndex'   => $endIndex
        ));
    }

    /**
     * Edits an existing AvailabilitySchedule entity.
     *
     * @param Request $request The user's request object
     * @param integer $spid    Schedule period ID from request
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $spid)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        $entity = $em->getRepository('OpenSkedgeBundle:AvailabilitySchedule')->findOneBy(array(
            'user' => $user->getId(),
            'schedulePeriod' => $spid
        ));

        if (!$entity instanceof AvailabilitySchedule) {
            throw $this->createNotFoundException('Unable to find AvailabilitySchedule entity.');
        }

        // If the user is not the same as the user assigned to $entity, kick them out!
        if ($user != $entity->getUser()) {
            throw new AccessDeniedException();
        }

        $appSettings = $this->get('app_settings')->getAppSettings();

        $resolution = $request->query->get('timeresolution', $appSettings->getDefaultTimeResolution());

        // Get the user's schedule positions
        $schedules = $em->getRepository('OpenSkedgeBundle:Schedule')->findBy(array('schedulePeriod' => $spid, 'user' => $user->getId()));

        $deleteForm = $this->createDeleteForm($user->getId(), $spid);

        if ($request->getMethod() == 'POST') {
            $data = $request->request->get('day');
            for ($i = 0; $i < 7; $i++) {
                $entity->setDay($i, $data[$i]);
            }
            $entity->setLastUpdated();

            $em->persist($entity);
            $em->flush();

            $notify = $request->request->get('notify', false);

            if ($notify) {
                $mailer = $this->container->get('notify_mailer');
                $mailer->notifyAvailabilityScheduleChange($entity);
            }

            return $this->redirect($this->generateUrl('user_schedule_view', array(
                'uid'=> $user->getId(),
                'spid' => $spid
            )));
        }

        $dtUtils = $this->get('dt_utils');

        $startIndex = $dtUtils->getIndexFromTime($appSettings->getStartHour());
        $endIndex = $dtUtils->getIndexFromTime($appSettings->getEndHour())-1;

        $hoursAvailable = 0;
        for ($day = 0; $day < 7; $day++) {
            $timeRec = $entity->getDay($day);
            $hoursAvailable += substr_count($timeRec, '1') + substr_count($timeRec, '2') + substr_count($timeRec, '3');
        }
        $hoursAvailable = $hoursAvailable / 4;

        $hoursScheduled = 0;
        foreach($schedules as $schedule) {
            $scheduleSum = 0;
            for ($day = 0; $day < 7; $day++) {
                $scheduleSum += substr_count($schedule->getDay($day), '1');
            }
            $hoursScheduled += $scheduleSum / 4;
        }

        return $this->render('OpenSkedgeBundle:AvailabilitySchedule:edit.html.twig', array(
            'htime'       => $dtUtils->timeStrToDateTime($appSettings->getStartHour()),
            'resolution'  => $resolution,
            'avail'       => $entity,
            'edit'        => true,
            'schedules'   => $schedules,
            'delete_form' => $deleteForm->createView(),
            'startIndex'  => $startIndex,
            'endIndex'    => $endIndex,
            'hrsAvail'    => $hoursAvailable,
            'hrsSched'    => $hoursScheduled
        ));
    }

    /**
     * Deletes a AvailabilitySchedule entity and the associated Schedule entities.
     *
     * @param Request $request The user's request object
     * @param integer $uid     User ID from route
     * @param integer $spid    Schedule period ID from route
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, $uid, $spid)
    {
        $form = $this->createDeleteForm($uid, $spid);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $availabilitySchedule = $em->getRepository('OpenSkedgeBundle:AvailabilitySchedule')->findOneBy(array('user' => $uid, 'schedulePeriod' => $spid));
            $schedules = $em->getRepository('OpenSkedgeBundle:Schedule')->findBy(array('user' => $uid, 'schedulePeriod' => $spid));

            if (!$availabilitySchedule instanceof AvailabilitySchedule) {
                throw $this->createNotFoundException('Unable to find AvailabilitySchedule entity.');
            }

            if ($this->getUser() != $availabilitySchedule->getUser()) {
                throw new AccessDeniedException();
            }

            foreach ($schedules as $schedule) {
                $em->remove($schedule);
            }

            $em->remove($availabilitySchedule);

            $em->flush();

            $request->getSession()->getFlashBag()->add('success', 'Availability schedule deleted successfully.');
        } else {
            $request->getSession()->getFlashBag()->add('error', 'Availability schedule could not be deleted! If the issue persists, please report it to your friendly sysadmin.');
        }

        return $this->redirect($this->generateUrl('user_schedules', array('id' => $uid)));
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
