<?php

namespace OpenSkedge\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use OpenSkedge\AppBundle\Entity\Settings;
use OpenSkedge\AppBundle\Form\SettingsType;

/**
 * Controller for manipulating application settings
 *
 * @category Controller
 * @package  OpenSkedge\AppBundle\Controller
 * @author   Max Fierke <max@maxfierke.com>
 * @license  GNU General Public License, version 3
 * @version  GIT: $Id$
 * @link     https://github.com/maxfierke/OpenSkedge OpenSkedge Github
 */
class SettingsController extends Controller
{

    /**
     * Edit application settings
     *
     * @param Request $request The user's request object
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request)
    {
        // Only allow access to Administrators
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

                $request->getSession()->getFlashBag()->add('success', 'Application settings updated successfully.');

                return $this->redirect($this->generateUrl('app_settings_edit'));
            }
            $request->getSession()->getFlashBag()->add('error', 'Application settings could not be updated. Check for form errors below. If the issue persists, please report it to your friendly sysadmin.');
        }

        return $this->render('OpenSkedgeBundle:Settings:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }
}
