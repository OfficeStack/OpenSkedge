<?php

namespace OpenSkedge\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use OpenSkedge\AppBundle\Entity\Area;
use OpenSkedge\AppBundle\Entity\Position;
use OpenSkedge\AppBundle\Entity\User;
use OpenSkedge\AppBundle\Form\PositionType;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\ArrayAdapter;

/**
 * Controller for CRUD operations on Position entities
 *
 * @category Controller
 * @package  OpenSkedge\AppBundle\Controller
 * @author   Max Fierke <max@maxfierke.com>
 * @license  GNU General Public License, version 3
 * @version  GIT: $Id$
 * @link     https://github.com/maxfierke/OpenSkedge OpenSkedge Github
 */
class PositionController extends Controller
{
    /**
     * Lists all Position entities.
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $positions = $em->getRepository('OpenSkedgeBundle:Position')->findAll();

        $page = $this->container->get('request')->query->get('page', 1);

        $adapter = new ArrayAdapter($positions);
        $paginator = new Pagerfanta($adapter);
        $paginator->setMaxPerPage(15);
        $paginator->setCurrentPage($page);

        $entities = $paginator->getCurrentPageResults();

        return $this->render('OpenSkedgeBundle:Position:index.html.twig', array(
            'entities' => $entities,
            'paginator' => $paginator,
        ));
    }

    /**
     * Finds and displays a Position entity.
     *
     * @param integer $id ID of Position entity
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function viewAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OpenSkedgeBundle:Position')->find($id);

        if (!$entity instanceof Position) {
            throw $this->createNotFoundException('Unable to find Position entity.');
        }

        $allSchedulePeriods = $em->getRepository('OpenSkedgeBundle:SchedulePeriod')->findBy(array(), array(
            'endTime' => 'DESC'
        ));

        $page = $this->container->get('request')->query->get('page', 1);

        $adapter = new ArrayAdapter($allSchedulePeriods);
        $paginator = new Pagerfanta($adapter);
        $paginator->setMaxPerPage(5);
        $paginator->setCurrentPage($page);

        $schedulePeriods = $paginator->getCurrentPageResults();

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('OpenSkedgeBundle:Position:view.html.twig', array(
            'entity'           => $entity,
            'delete_form'      => $deleteForm->createView(),
            'schedulePeriods'  => $schedulePeriods,
            'paginator'        => $paginator,
        ));
    }

    /**
     * Creates a new Position entity.
     *
     * @param Request $request The user's request object
     * @param integer $aid     ID of the parent Area entity
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request, $aid)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        $area = $em->getRepository('OpenSkedgeBundle:Area')->find($aid);

        if (!$area instanceof Area) {
            throw $this->createNotFoundException('Unable to find Area entity.');
        }

        $entity  = new Position();
        $form = $this->createForm(new PositionType(), $entity);

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                $request->getSession()->setFlash('success', $entity->getArea()->getName().' - '.$entity->getName().' created successfully.');

                return $this->redirect($this->generateUrl('position_view', array('id' => $entity->getId())));
            }
            $request->getSession()->setFlash('error', 'Position could not be created. Check for form errors below. If the issue persists, please report it to your friendly sysadmin.');
        }

        return $this->render('OpenSkedgeBundle:Position:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'area'   => $area,
        ));
    }

    /**
     * Edits an existing Position entity.
     *
     * @param Request $request The user's request object
     * @param integer $id      ID of Position entity
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OpenSkedgeBundle:Position')->find($id);

        if (!$entity instanceof Position) {
            throw $this->createNotFoundException('Unable to find Position entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new PositionType(), $entity);

        if ($request->getMethod() == 'POST') {
            $editForm->bind($request);

            if ($editForm->isValid()) {
                $em->persist($entity);
                $em->flush();

                $request->getSession()->setFlash('success', $entity->getArea()->getName().' - '.$entity->getName().' updated successfully.');

                return $this->redirect($this->generateUrl('area_view', array('id' => $entity->getArea()->getId())));
            }
            $request->getSession()->setFlash('error', 'Position could not be updated. Check for form errors below. If the issue persists, please report it to your friendly sysadmin.');
        }

        return $this->render('OpenSkedgeBundle:Position:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Position entity.
     *
     * @param Request $request The user's request object
     * @param integer $id      ID of Position entity
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, $id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('OpenSkedgeBundle:Position')->find($id);

            if (!$entity instanceof Position) {
                throw $this->createNotFoundException('Unable to find Position entity.');
            }

            $em->remove($entity);
            $em->flush();
            $request->getSession()->setFlash('success', 'Position deleted successfully.');
        } else {
            $request->getSession()->setFlash('error', 'Position could not be deleted. If the issue persists, please report it to your friendly sysadmin.');
        }

        return $this->redirect($this->generateUrl('areas'));
    }

    /**
     * Finds and displays a user's scheduled positions
     *
     * @param integer $id ID of user
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function positionsAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if (is_null($id)) {
            $user = $this->getUser();
            $userstitle = 'My Positions';
        } else {
            $user = $em->getRepository('OpenSkedgeBundle:User')->find($id);

            if (!$user instanceof User) {
                throw $this->createNotFoundException('Unable to find User.');
            }

            $userstitle = $user->getName()."'s Positions";
        }

        $qb = $em->createQueryBuilder();

        // Grab all current schedule periods
        $schedulePeriods = $qb->select('sp')
            ->from('OpenSkedgeBundle:SchedulePeriod', 'sp')
            ->where('sp.startTime < CURRENT_TIMESTAMP()')
            ->andWhere('sp.endTime > CURRENT_TIMESTAMP()')
            ->getQuery()
            ->getResult();

        // Get all the schedules for each schedule period
        $schedules = array();
        foreach ($schedulePeriods as $schedulePeriod) {
            $schedules[] = $em->getRepository('OpenSkedgeBundle:Schedule')->findBy(array(
                'schedulePeriod' => $schedulePeriod->getId(),
                'user' => $user->getId(),
            ));
        }

        // Get the position for each schedule
        $positions = array();
        for ($i=0; $i<count($schedules); $i++) {
            foreach($schedules[$i] as $schedule)
            {
                $positions[] = $schedule->getPosition();
            }
        }

        // Remove duplicate positions
        $positions = array_unique($positions);

        $page = $this->container->get('request')->query->get('page', 1);

        $adapter = new ArrayAdapter($positions);
        $paginator = new Pagerfanta($adapter);
        $paginator->setMaxPerPage(15);
        $paginator->setCurrentPage($page);

        $entities = $paginator->getCurrentPageResults();

        return $this->render('OpenSkedgeBundle:Position:index.html.twig', array(
            'userstitle' => $userstitle,
            'entities'   => $entities,
            'paginator'  => $paginator,
        ));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))->add('id', 'hidden')->getForm();
    }
}
