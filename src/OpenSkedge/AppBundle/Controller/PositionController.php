<?php

namespace OpenSkedge\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use OpenSkedge\AppBundle\Entity\Position;
use OpenSkedge\AppBundle\Form\PositionType;

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

        $entities = $em->getRepository('OpenSkedgeBundle:Position')->findAll();

        return $this->render('OpenSkedgeBundle:Position:index.html.twig', array(
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

        $entity = $em->getRepository('OpenSkedgeBundle:Position')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Position entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('OpenSkedgeBundle:Position:view.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Creates a new Position entity.
     *
     */
    public function newAction(Request $request, $area_id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

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

        return $this->render('OpenSkedgeBundle:Position:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'area_id' => $area_id
        ));
    }

    /**
     * Edits an existing Position entity.
     *
     */
    public function editAction(Request $request, $id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OpenSkedgeBundle:Position')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Position entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new PositionType(), $entity);

        if ($request->getMethod() == 'POST') {
            $editForm->bind($request);

            if ($editForm->isValid()) {
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('area_view', array('id' => $entity->getArea()->getId())));
            }
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

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Position entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('position'));
    }

    public function positionsAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if(is_null($id)) {
            $user = $this->getUser();
            $userstitle = 'My Positions';
        } else {
            $user = $em->getRepository('OpenSkedgeBundle:User')->find($id);

            if (!$user) {
                throw $this->createNotFoundException('Unable to find User.');
            }

            $userstitle = $user->getName()."'s Positions";
        }

        $qb = $em->createQueryBuilder();

        $time = time();

        $schedulePeriods = $qb->select('sp')
                              ->from('OpenSkedgeBundle:SchedulePeriod', 'sp')
                              ->where('sp.startTime < CURRENT_TIMESTAMP()')
                              ->andWhere('sp.endTime > CURRENT_TIMESTAMP()')
                              ->getQuery()
                              ->getResult();
        $schedules = array();

        foreach($schedulePeriods as $schedulePeriod) {
            $schedules[] = $em->getRepository('OpenSkedgeBundle:Schedule')->findBy(array(
                'schedulePeriod' => $schedulePeriod->getId(),
                'user' => $user->getId(),
            ));
        }

        $entities = array();

        for ($i=0; $i<count($schedules); $i++) {
            foreach($schedules[$i] as $schedule)
            {
                $entities[] = $schedule->getPosition();
            }
        }

        $entities = array_unique($entities);

        return $this->render('OpenSkedgeBundle:Position:index.html.twig', array(
            'userstitle' => $userstitle,
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
