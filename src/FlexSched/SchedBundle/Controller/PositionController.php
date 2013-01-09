<?php

namespace FlexSched\SchedBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use FlexSched\SchedBundle\Entity\Position;
use FlexSched\SchedBundle\Form\PositionType;

/**
 * Position controller.
 *
 */
class PositionController extends Controller
{
    /**
     * Lists all Position entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FlexSchedBundle:Position')->findAll();

        return $this->render('FlexSchedBundle:Position:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Finds and displays a Position entity.
     *
     */
    public function viewAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FlexSchedBundle:Position')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Position entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FlexSchedBundle:Position:view.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to create a new Position entity.
     *
     */
    public function newAction()
    {
        $entity = new Position();
        $form   = $this->createForm(new PositionType(), $entity);

        return $this->render('FlexSchedBundle:Position:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new Position entity.
     *
     */
    public function createAction(Request $request, $area_id)
    {
        $entity  = new Position();
        $form = $this->createForm(new PositionType(), $entity);

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('position_view', array('id' => $entity->getId())));
            }
        }

        return $this->render('FlexSchedBundle:Position:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'area_id' => $area_id
        ));
    }

    /**
     * Displays a form to edit an existing Position entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FlexSchedBundle:Position')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Position entity.');
        }

        $editForm = $this->createForm(new PositionType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FlexSchedBundle:Position:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Position entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FlexSchedBundle:Position')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Position entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new PositionType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('position_edit', array('id' => $id)));
        }

        return $this->render('FlexSchedBundle:Position:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Position entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FlexSchedBundle:Position')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Position entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('position'));
    }

    public function positionsAction()
    {
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();

        $qb = $em->createQueryBuilder();

        $time = time();

        $schedulePeriods = $qb->select('sp')
                              ->from('FlexSchedBundle:SchedulePeriod', 'sp')
                              ->where('sp.startTime < CURRENT_TIMESTAMP()')
                              ->andWhere('sp.endTime > CURRENT_TIMESTAMP()')
                              ->getQuery()
                              ->getResult();
        $schedules = array();

        foreach($schedulePeriods as $schedulePeriod) {
            $schedules[] = $em->getRepository('FlexSchedBundle:Schedule')->findBySchedulePeriod($schedulePeriod);
        }

        $entities = array();

        for ($i=0; $i<count($schedules); $i++) {
            foreach($schedules[$i] as $schedule)
            {
                $entities[] = $schedule->getPosition();
            }
        }

        $entities = array_unique($entities);

        return $this->render('FlexSchedBundle:Position:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
