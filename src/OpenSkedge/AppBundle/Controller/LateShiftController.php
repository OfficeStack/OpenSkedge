<?php

namespace OpenSkedge\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use OpenSkedge\AppBundle\Entity\LateShift;
use OpenSkedge\AppBundle\Form\LateShiftType;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Exception\NotValidMaxPerPageException;
use Pagerfanta\Exception\NotValidCurrentPageException;
use Pagerfanta\Adapter\DoctrineORMAdapter;

/**
 * Controller for OpenSkedge LateShift entities
 *
 * @category Controller
 * @package  OpenSkedge\AppBundle\Controller
 * @author   Max Fierke <max@maxfierke.com>
 * @license  GNU General Public License, version 3
 * @version  GIT: $Id$
 * @link     https://github.com/maxfierke/OpenSkedge OpenSkedge Github
 */
class LateShiftController extends Controller
{
    /**
     * Lists all LateShift entities.
     *
     */
    public function indexAction()
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        $missedAndLateShiftsQB = $em->createQueryBuilder()
            ->select('late')
            ->from('OpenSkedgeBundle:LateShift', 'late')
            ->orderBy('late.creationTime', 'DESC');

        $page = $this->container->get('request')->query->get('page', 1);
        $limit = $this->container->get('request')->query->get('limit', 35);

        $adapter = new DoctrineORMAdapter($missedAndLateShiftsQB);
        $paginator = new Pagerfanta($adapter);

        try {
            $paginator->setMaxPerPage($limit);
            $paginator->setCurrentPage($page);

            $entities = $paginator->getCurrentPageResults();
        } catch (NotValidMaxPerPageException $e) {
            throw new HttpException(400, 'Not a valid limit', $e, array(), $e->getCode());
        } catch (NotValidCurrentPageException $e) {
            throw $this->createNotFoundException('Page does not exist.');
        }

        return $this->render('OpenSkedgeBundle:LateShift:index.html.twig', array(
            'entities'    => $entities,
            'paginator'   => $paginator,
            'title'       => "Late and Missed Shifts",
        ));
    }

    /**
     * Lists all late shifts.
     *
     */
    public function lateAction()
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        $lateShiftsQB = $em->createQueryBuilder()
            ->select('late')
            ->from('OpenSkedgeBundle:LateShift', 'late')
            ->where('late.arrivalTime IS NOT NULL')
            ->orderBy('late.creationTime', 'DESC');

        $page = $this->container->get('request')->query->get('page', 1);
        $limit = $this->container->get('request')->query->get('limit', 35);

        $adapter = new DoctrineORMAdapter($lateShiftsQB);
        $paginator = new Pagerfanta($adapter);

        try {
            $paginator->setMaxPerPage($limit);
            $paginator->setCurrentPage($page);

            $entities = $paginator->getCurrentPageResults();
        } catch (NotValidMaxPerPageException $e) {
            throw new HttpException(400, 'Not a valid limit', $e, array(), $e->getCode());
        } catch (NotValidCurrentPageException $e) {
            throw $this->createNotFoundException('Page does not exist.');
        }

        return $this->render('OpenSkedgeBundle:LateShift:index.html.twig', array(
            'entities'    => $entities,
            'paginator'   => $paginator,
            'title'       => "Late Users",
        ));
    }

    /**
     * Lists all missed shifts.
     *
     */
    public function missedAction()
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        $missedShiftsQB = $em->createQueryBuilder()
            ->select('late')
            ->from('OpenSkedgeBundle:LateShift', 'late')
            ->where('late.arrivalTime IS NULL')
            ->andWhere('DATE_DIFF(CURRENT_DATE(), late.creationTime) != 0')
            ->orderBy('late.creationTime', 'DESC');

        $page = $this->container->get('request')->query->get('page', 1);
        $limit = $this->container->get('request')->query->get('limit', 35);

        $adapter = new DoctrineORMAdapter($missedShiftsQB);
        $paginator = new Pagerfanta($adapter);

        try {
            $paginator->setMaxPerPage($limit);
            $paginator->setCurrentPage($page);

            $entities = $paginator->getCurrentPageResults();
        } catch (NotValidMaxPerPageException $e) {
            throw new HttpException(400, 'Not a valid limit', $e, array(), $e->getCode());
        } catch (NotValidCurrentPageException $e) {
            throw $this->createNotFoundException('Page does not exist.');
        }

        return $this->render('OpenSkedgeBundle:LateShift:index.html.twig', array(
            'entities'    => $entities,
            'paginator'   => $paginator,
            'title'       => "Late Users",
        ));
    }

    /**
     * Edits an existing LateShift entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OpenSkedgeBundle:LateShift')->find($id);

        if (!$entity instanceof LateShift) {
            throw $this->createNotFoundException('Unable to find LateShift entity.');
        }

        $editForm = $this->createForm(new LateShiftType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            $request->getSession()->setFlash('success', 'Late shift updated successfully.');
        } else {
            $request->getSession()->setFlash('error', 'Late shift failed to update!');
        }

        $referer = $request->headers->get('referer');

        return new RedirectResponse($referer);
    }

    /**
     * Deletes a LateShift entity.
     *
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
            $entity = $em->getRepository('OpenSkedgeBundle:LateShift')->find($id);

            if (!$entity instanceof LateShift) {
                throw $this->createNotFoundException('Unable to find LateShift entity.');
            }

            $em->remove($entity);
            $em->flush();

            $request->getSession()->setFlash('success', 'Late shift clock-in record deleted successfully.');
        } else {
            $request->getSession()->setFlash('error', 'Late shift clock-in record could not be deleted. If the issue persists, please report it to your friendly sysadmin.');
        }

        return $this->redirect($this->generateUrl('shifts_late'));
    }

    /**
     * Creates a form to delete a LateShift entity by id.
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
