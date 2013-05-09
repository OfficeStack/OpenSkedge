<?php

namespace OpenSkedge\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use OpenSkedge\AppBundle\Entity\Schedule;
use OpenSkedge\AppBundle\Entity\Shift;
use OpenSkedge\AppBundle\Form\ShiftType;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\ArrayAdapter;

/**
 * Controller for OpenSkedge Shift entities
 *
 * @category Controller
 * @package  OpenSkedge\AppBundle\Controller
 * @author   Max Fierke <max@maxfierke.com>
 * @license  GNU General Public License, version 3
 * @version  GIT: $Id$
 * @link     https://github.com/maxfierke/OpenSkedge OpenSkedge Github
 */
class ShiftController extends Controller
{
    /**
     * Lists all Shift entities.
     *
     */
    public function indexAction()
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        $shifts = $em->getRepository('OpenSkedgeBundle:Shift')->findAll();

        $page = $this->container->get('request')->query->get('page', 1);

        $adapter = new ArrayAdapter($shifts);
        $paginator = new Pagerfanta($adapter);
        $paginator->setMaxPerPage(35);
        $paginator->setCurrentPage($page);

        $entities = $paginator->getCurrentPageResults();

        return $this->render('OpenSkedgeBundle:Shift:index.html.twig', array(
            'entities'    => $entities,
            'paginator'   => $paginator,
        ));
    }

    /**
     * Lists all Shift entities.
     *
     */
    public function postedAction()
    {
        $em = $this->getDoctrine()->getManager();

        $postedShifts = $em->createQuery('SELECT shift FROM OpenSkedgeBundle:Shift shift
                                          WHERE (shift.endTime > CURRENT_TIMESTAMP() AND shift.status != \'unapproved\')')
            ->getResult();

        $page = $this->container->get('request')->query->get('page', 1);

        $adapter = new ArrayAdapter($postedShifts);
        $paginator = new Pagerfanta($adapter);
        $paginator->setMaxPerPage(15);
        $paginator->setCurrentPage($page);

        $entities = $paginator->getCurrentPageResults();

        $entity = new Shift();
        $now = new \DateTime("now");
        $entity->setStartTime($now);
        $entity->setEndTime($now);
        $newForm = $this->createForm(new ShiftType(), $entity);
        $newForm->remove('pickedUpBy');

        return $this->render('OpenSkedgeBundle:Shift:posted.html.twig', array(
            'entities'    => $entities,
            'paginator'   => $paginator,
            'newForm'     => $newForm->createView(),
        ));
    }

    /**
     * Creates a new Shift entity.
     *
     */
    public function createAction(Request $request)
    {
        $shift = $request->request->get('shift');
        $shift['startTime']['time'] = date("H:i", strtotime($shift['startTime']['time']));
        $shift['endTime']['time'] = date("H:i", strtotime($shift['endTime']['time']));
        $request->request->set('shift', $shift);

        $entity  = new Shift();
        $form = $this->createForm(new ShiftType(), $entity);
        $form->remove('pickedUpBy');
        $form->bind($request);

        $referer = $request->headers->get('referer');

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            try {
                $schedule = $em->createQuery('SELECT s FROM OpenSkedgeBundle:Schedule s
                        WHERE (s.user = :uid AND s.position = :pid AND s.schedulePeriod = :spid)')
                    ->setParameter('uid', $this->getUser()->getId())
                    ->setParameter('pid', $entity->getPosition()->getId())
                    ->setParameter('spid', $entity->getSchedulePeriod()->getId())
                    ->getSingleResult();
            } catch (\Doctrine\ORM\NoResultException $e) {
                $request->getSession()->setFlash('error', 'Shift could not be posted! Your\'re not scheduled for the selected position in the selected scheduling period.');
                return new RedirectResponse($referer);
            }

            $startDT = $entity->getStartTime();
            $endDT = $entity->getEndTime();

            if ($startDT->format('Y-m-d') != $endDT->format('Y-m-d')) {
                $request->getSession()->setFlash('error', 'Shift could not be posted! Shift start and end times must be on the same date.');
                return new RedirectResponse($referer);
            }

            $intervals = $this->get('dt_utils')->getDateTimeIntervals($schedule->getDay($startDT->format('w')), (int)$startDT->format('w'));
            $inInterval = false;
            foreach($intervals as $interval) {
                // Set the interval dates to the target date.
                $interval[0]->setDate($startDT->format('Y'), $startDT->format('n'), $startDT->format('j'));
                $interval[1]->setDate($endDT->format('Y'), $endDT->format('n'), $endDT->format('j'));

                // Check if the shift the user is posting is in one of the positions scheduled intervals.
                if (($startDT >= $interval[0] and $startDT < $interval[1]) and ($endDT > $interval[0] and $endDT <= $interval[1])) {
                    $inInterval = true;
                    break;
                }
            }

            if(!$inInterval) {
                $request->getSession()->setFlash('error', 'Shift could not be posted! You cannot put up a shift that you\'re not scheduled for.');
                return new RedirectResponse($referer);
            }

            $entity->setUser($this->getUser());
            $entity->setSchedule($schedule);

            $em->persist($entity);
            $em->flush();

            $request->getSession()->setFlash('success', 'Shift posted successfully.');
        } else {
            $request->getSession()->setFlash('error', 'Shift could not be posted! Invalid data given.');
        }

        return new RedirectResponse($referer);
    }

    /**
     * Finds and displays a Shift entity.
     *
     */
    public function viewAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OpenSkedgeBundle:Shift')->find($id);

        if (!$entity instanceof Shift) {
            throw $this->createNotFoundException('Unable to find Shift entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('OpenSkedgeBundle:Shift:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Shift entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OpenSkedgeBundle:Shift')->find($id);

        if (!$entity instanceof Shift) {
            throw $this->createNotFoundException('Unable to find Shift entity.');
        }

        $editForm = $this->createForm(new ShiftType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('OpenSkedgeBundle:Shift:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Shift entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OpenSkedgeBundle:Shift')->find($id);

        if (!$entity instanceof Shift) {
            throw $this->createNotFoundException('Unable to find Shift entity.');
        }

        $referer = $request->headers->get('referer');

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new ShiftType(), $entity);
        if (!is_null($entity->getPickedUpBy()) and $entity->getPickedUpBy()->getId() != $this->getUser()->getId() and false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $request->getSession()->setFlash('error', 'Shift has been picked up and cannot be modified!');
            return new RedirectResponse($referer);
        } elseif ($entity->getUser()->getId() != $this->getUser()->getId() and false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $editForm->remove('startTime');
            $editForm->remove('endTime');
            $editForm->remove('notes');
            $editForm->remove('schedulePeriod');
            $editForm->remove('position');
            $editForm->remove('status');
        } elseif ($entity->getUser()->getId() == $this->getUser()->getId() and false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $editForm->remove('status');
            $editForm->remove('pickedUpBy');
        } else {
            $editForm->remove('startTime');
            $editForm->remove('endTime');
            $editForm->remove('notes');
            $editForm->remove('schedulePeriod');
            $editForm->remove('position');
            $editForm->remove('pickedUpBy');
        }
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            $request->getSession()->setFlash('success', 'Shift updated successfully.');
        } else {
            var_dump($editForm->getErrorsAsString(4));
            $request->getSession()->setFlash('error', 'Shift failed to update!');
        }

        return new RedirectResponse($referer);
    }

    /**
     * Deletes a Shift entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('OpenSkedgeBundle:Shift')->find($id);

            if (!$entity instanceof Shift) {
                throw $this->createNotFoundException('Unable to find Shift entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('shift'));
    }

    /**
     * Creates a form to delete a Shift entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))->add('id', 'hidden')->getForm();
    }
}
