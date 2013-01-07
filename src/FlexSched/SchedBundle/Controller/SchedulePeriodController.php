<?php

namespace FlexSched\SchedBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use FlexSched\SchedBundle\Entity\SchedulePeriod;
use FlexSched\SchedBundle\Form\SchedulePeriodType;

/**
 * SchedulePeriod controller.
 *
 */
class SchedulePeriodController extends Controller
{
    /**
     * Lists all SchedulePeriod entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FlexSchedBundle:SchedulePeriod')->findAll();

        return $this->render('FlexSchedBundle:SchedulePeriod:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Finds and displays a SchedulePeriod entity.
     *
     */
    public function viewAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FlexSchedBundle:SchedulePeriod')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SchedulePeriod entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FlexSchedBundle:SchedulePeriod:view.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to create a new SchedulePeriod entity.
     *
     */
    public function newAction()
    {
        $entity = new SchedulePeriod();
        $form   = $this->createForm(new SchedulePeriodType(), $entity);

        return $this->render('FlexSchedBundle:SchedulePeriod:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new SchedulePeriod entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity  = new SchedulePeriod();
        $form = $this->createForm(new SchedulePeriodType(), $entity);

        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('schedule_period_view', array('id' => $entity->getId())));
            }
        }

        return $this->render('FlexSchedBundle:SchedulePeriod:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing SchedulePeriod entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FlexSchedBundle:SchedulePeriod')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SchedulePeriod entity.');
        }

        $editForm = $this->createForm(new SchedulePeriodType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FlexSchedBundle:SchedulePeriod:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing SchedulePeriod entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FlexSchedBundle:SchedulePeriod')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SchedulePeriod entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new SchedulePeriodType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('schedule_period_edit', array('id' => $id)));
        }

        return $this->render('FlexSchedBundle:SchedulePeriod:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a SchedulePeriod entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FlexSchedBundle:SchedulePeriod')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find SchedulePeriod entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('schedule_period'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
