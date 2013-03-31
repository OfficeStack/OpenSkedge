<?php

namespace OpenSkedge\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use OpenSkedge\AppBundle\Entity\IP;
use OpenSkedge\AppBundle\Form\IPType;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\ArrayAdapter;

/**
 * IP controller.
 *
 */
class IPController extends Controller
{
    /**
     * Lists all IP entities.
     *
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
     */
    public function newAction(Request $request)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedException();
        }

        $entity  = new IP();
        $form = $this->createForm(new IPType(), $entity);

        $clientIp = (isset($_ENV['PAGODABOX']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $request->getClientIp());

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('app_settings_ips'));
            }
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
     */
    public function editAction(Request $request, $id)
    {
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

                return $this->redirect($this->generateUrl('app_settings_ips', array('id' => $id)));
            }
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
        }

        return $this->redirect($this->generateUrl('app_settings_ips'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
