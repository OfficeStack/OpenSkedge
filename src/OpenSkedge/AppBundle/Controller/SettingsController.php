<?php

namespace OpenSkedge\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use OpenSkedge\AppBundle\Entity\Settings;
use OpenSkedge\AppBundle\Form\SettingsType;

/**
 * Settings controller.
 *
 */
class SettingsController extends Controller
{

    /**
     * Edits an existing Settings entity.
     *
     */
    public function editAction(Request $request)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OpenSkedgeBundle:Settings')->find(1);

        if (!$entity instanceof Settings) {
            throw $this->createNotFoundException('Unable to find Settings entity.');
        }

        $editForm = $this->createForm(new SettingsType(), $entity);

        if ($request->getMethod() == 'POST') {
            $editForm->bind($request);
            if ($editForm->isValid()) {
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('app_settings_edit'));
            }
        }

        return $this->render('OpenSkedgeBundle:Settings:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }
}
