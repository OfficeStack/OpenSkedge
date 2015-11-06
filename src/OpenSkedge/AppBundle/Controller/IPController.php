<?php

namespace OpenSkedge\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use OpenSkedge\AppBundle\Entity\IP;
use OpenSkedge\AppBundle\Form\IPType;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\ArrayAdapter;

/**
 * Controller for CRUD operations on IP entities
 *
 * @category Controller
 * @package  OpenSkedge\AppBundle\Controller
 * @author   Max Fierke <max@maxfierke.com>
 * @license  GNU General Public License, version 3
 * @version  GIT: $Id$
 * @link     https://github.com/maxfierke/OpenSkedge OpenSkedge Github
 */
class IPController extends Controller
{
    /**
     * Lists all IP entities.
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws AccessDeniedException
     */
    public function indexAction()
    {
        if (false === $this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        $ips = $em->getRepository('OpenSkedgeBundle:IP')->findAll();

        $page = $this->container->get('request')->query->get('page', 1);

        $adapter = new ArrayAdapter($ips);
        $paginator = new Pagerfanta($adapter);
        $paginator->setMaxPerPage(15);
        $paginator->setCurrentPage($page);

        $entities = $paginator->getCurrentPageResults();

        // Create a deletion form object for each entity to be rendered by Twig
        $deleteForms = array();
        foreach ($entities as $entity) {
            $deleteForms[] = $this->createDeleteForm($entity->getId())->createView();
        }

        return $this->render('OpenSkedgeBundle:IP:index.html.twig', array(
            'entities'    => $entities,
            'deleteForms' => $deleteForms,
            'paginator'   => $paginator,
        ));
    }

    /**
     * Creates a new IP entity.
     *
     * @param Request $request The user's request object
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws AccessDeniedException
     */
    public function newAction(Request $request)
    {
        // Only administrators are allowed here. Kick everyone else out.
        if (false === $this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedException();
        }

        $entity  = new IP();
        $form = $this->createForm(new IPType(), $entity);

        /* If running on Pagoda Box, get the user's IP directly from HTTP_X_FORWARDED_FOR,
         * otherwise, go to Request::getClientIp()
         */
        $clientIp = (isset($_ENV['PAGODABOX']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $request->getClientIp());

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                $request->getSession()->getFlashBag()->add('success', $entity->getIp().' added successfully.');

                return $this->redirect($this->generateUrl('app_settings_ips'));
            }

            $request->getSession()->getFlashBag()->add('error', 'IP address could not be added to the manifest. Check for form errors below. If the issue persists, please report it to your friendly sysadmin.');
        }

        return $this->render('OpenSkedgeBundle:IP:new.html.twig', array(
            'entity'    => $entity,
            'clientip'  => $clientIp,
            'form'      => $form->createView(),
        ));
    }

    /**
     * Edits an existing IP entity.
     *
     * @param Request $request The user's request object
     * @param integer $id ID of IP entity
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws AccessDeniedException
     */
    public function editAction(Request $request, $id)
    {
        // Only administrators are allowed here. Kick everyone else out.
        if (false === $this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OpenSkedgeBundle:IP')->find($id);

        if (!$entity instanceof IP) {
            throw $this->createNotFoundException('Unable to find IP entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new IPType(), $entity);

        if ($request->getMethod() == 'POST') {
            $editForm->bind($request);
            if ($editForm->isValid()) {
                $em->persist($entity);
                $em->flush();

                $request->getSession()->getFlashBag()->add('success', $entity->getIp().' updated successfully.');

                return $this->redirect($this->generateUrl('app_settings_ips', array('id' => $id)));
            }
            $request->getSession()->getFlashBag()->add('error', $entity->getIp().' could not be updated. Check for form errors below. If the issue persists, please report it to your friendly sysadmin.');
        }

        return $this->render('OpenSkedgeBundle:IP:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a IP entity.
     *
     * @param Request $request The user's request object
     * @param integer $id ID of IP entity
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws AccessDeniedException
     */
    public function deleteAction(Request $request, $id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedException();
        }

        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('OpenSkedgeBundle:IP')->find($id);

            if (!$entity instanceof IP) {
                throw $this->createNotFoundException('Unable to find IP entity.');
            }

            $em->remove($entity);
            $em->flush();

            $request->getSession()->getFlashBag()->add('success', 'IP address removed from manifest successfully.');
        } else {
            $request->getSession()->getFlashBag()->add('error', 'IP address could not be removed. If the issue persists, please report it to your friendly sysadmin.');
        }

        return $this->redirect($this->generateUrl('app_settings_ips'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))->add('id', 'hidden')->getForm();
    }
}
