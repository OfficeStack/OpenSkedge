<?php

namespace OpenSkedge\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use OpenSkedge\AppBundle\Entity\User;
use OpenSkedge\AppBundle\Entity\Clock;
use OpenSkedge\AppBundle\Form\UserType;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\Form;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Adapter\DoctrineCollectionAdapter;

/**
 * Controller for CRUD operations on User entities
 *
 * @category Controller
 * @package  OpenSkedge\AppBundle\Controller
 * @author   Max Fierke <max@maxfierke.com>
 * @license  GNU General Public License, version 3
 * @version  GIT: $Id$
 * @link     https://github.com/maxfierke/OpenSkedge OpenSkedge Github
 */
class UserController extends Controller
{
    /**
     * Lists all User entities.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        $usersQB = $em->createQueryBuilder()
            ->select('u')
            ->from('OpenSkedgeBundle:User', 'u')
            ->innerJoin('u.group', 'g')
            ->orderBy('u.name', 'ASC');

        $page = $this->container->get('request')->query->get('page', 1);

        $adapter = new DoctrineORMAdapter($usersQB);
        $paginator = new Pagerfanta($adapter);
        $paginator->setMaxPerPage(15);
        $paginator->setCurrentPage($page);

        $entities = $paginator->getCurrentPageResults();

        $entity  = new User();
        $form = $this->createForm(new UserType(), $entity, array(
            'validation_groups' => array('Default', 'user_creation')
        ));

        return $this->render('OpenSkedgeBundle:User:index.html.twig', array(
            'userstitle' => 'Users',
            'entities'   => $entities,
            'paginator'  => $paginator,
            'form'       => $form->createView()
        ));
    }

    /**
     * Finds and displays a User entity.
     *
     * @param integer $id ID of user
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if (is_null($id)) {
            $id = $this->getUser()->getId();
        }

        $entity = $em->getRepository('OpenSkedgeBundle:User')->find($id);

        if (!$entity instanceof User) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        return $this->render('OpenSkedgeBundle:User:view.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a new User entity.
     *
     * @param Request $request The user's request object
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $entity  = new User();
        $form = $this->createForm(new UserType(), $entity, array(
            'validation_groups' => array('Default', 'user_creation')
        ));

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $encoder = $this->get('security.encoder_factory')->getEncoder($entity);
                $plainPassword = $entity->getPassword();
                $password = $encoder->encodePassword($plainPassword, $entity->getSalt());
                $entity->setPassword($password);
                $em->persist($entity);
                $em->flush();
                $clock = new Clock();
                $clock->setUser($entity);
                $em->persist($clock);
                $em->flush();

                $mailer = $this->container->get('notify_mailer');
                $mailer->notifyUserCreation($entity, $plainPassword);

                $request->getSession()->getFlashBag()->add('success', $entity->getName().'\'s account created successfully.');

                return $this->redirect($this->generateUrl('users'));
            }
            $request->getSession()->getFlashBag()->add('error', 'User could not be created. Check for form errors below. If the issue persists, please report it to your friendly sysadmin.');
        }

        return $this->render('OpenSkedgeBundle:User:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Edits an existing User entity.
     *
     * @param Request $request The user's request object
     * @param integer $id      ID of user
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id)
    {
        // Only allow access if the user is authenticated as a supervisor, or it is their own profile.
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN') && $id != $this->getUser()->getId()) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OpenSkedgeBundle:User')->find($id);

        if (!$entity instanceof User) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $originalPassword = $entity->getPassword();

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new UserType(), $entity);
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $editForm->remove('color');
            $editForm->remove('min');
            $editForm->remove('max');
        }

        if ($id == $this->getUser()->getId()) {
            $editForm->remove('supnotes');
            $editForm->remove('group');
            $editForm->remove('supervisors');
            $editForm->remove('isActive');
        }

        if ($request->getMethod() == 'POST') {
            $editForm->bind($request);
            if ($editForm->isValid()) {
                $this->cleanupCollections($editForm);
                $plainPassword = $editForm->getViewData()->getPassword();
                if (!empty($plainPassword)) {
                    $encoder = $this->container->get('security.encoder_factory')->getEncoder($entity);
                    $password = $encoder->encodePassword($plainPassword, $entity->getSalt());
                    $entity->setPassword($password);
                } else {
                    $entity->setPassword($originalPassword);
                }
                $em->persist($entity);
                $em->flush();

                $request->getSession()->getFlashBag()->add('success', $entity->getName().'\'s account updated successfully.');

                return $this->redirect($this->generateUrl('user_view', array('id' => $id)));
            }
            $request->getSession()->getFlashBag()->add('error', 'User could not be updated. Check for form errors below. If the issue persists, please report it to your friendly sysadmin.');
        }

        return $this->render('OpenSkedgeBundle:User:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a User entity.
     *
     * @param Request $request The user's request object
     * @param integer $id      ID of user
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, $id)
    {
        // Only supervisors can delete other users, but not themselves.
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN') || $id == $this->getUser()->getId()) {
            throw new AccessDeniedException();
        }

        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('OpenSkedgeBundle:User')->find($id);

            if (!$entity instanceof User) {
                throw $this->createNotFoundException('Unable to find User entity.');
            }

            $em->remove($entity);
            $em->flush();
            $request->getSession()->getFlashBag()->add('success', 'User deleted successfully.');
        } else {
            $request->getSession()->getFlashBag()->add('error', 'User could not be deleted. If the issue persists, please report it to your friendly sysadmin.');
        }

        return $this->redirect($this->generateUrl('users'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))->add('id', 'hidden')->getForm();
    }

    /**
     * Lists all supervisors for the User.
     *
     * @param integer $id ID of user
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function supervisorsAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if (is_null($id)) {
            $user = $this->getUser();
            $userstitle = 'My Supervisors';
            $emptymsg = "You don't have any supervisors. You're such a boss!";
        } else {
            $user = $em->getRepository('OpenSkedgeBundle:User')->find($id);

            if (!$user) {
                throw $this->createNotFoundException('Unable to find User.');
            }

            $userstitle = $user->getName()."'s Supervisors";
            $emptymsg = $user->getName()." doesn't have any supervisors.";
        }

        $supervisors = $user->getSupervisors();
        $page = $this->container->get('request')->query->get('page', 1);

        $adapter = new DoctrineCollectionAdapter($supervisors);
        $paginator = new Pagerfanta($adapter);
        $paginator->setMaxPerPage(15);
        $paginator->setCurrentPage($page);

        $entities = $paginator->getCurrentPageResults();

        return $this->render('OpenSkedgeBundle:User:index.html.twig', array(
            'displayonly' => true,
            'userstitle'  => $userstitle,
            'entities'    => $entities,
            'paginator'   => $paginator,
            'emptymsg'    => $emptymsg,
        ));
    }

    /**
     * Lists all employees for the User.
     *
     * @param integer $id ID of user
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function employeesAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if (is_null($id)) {
            $user = $this->getUser();
            $userstitle = 'My Employees';
            $emptymsg = "You don't have any employees. Keep working! You'll get there one day!";
        } else {
            $user = $em->getRepository('OpenSkedgeBundle:User')->find($id);

            if (!$user) {
                throw $this->createNotFoundException('Unable to find User.');
            }

            $userstitle = $user->getName()."'s Employees";
            $emptymsg = $user->getName()." doesn't have any employees.";
        }

        $employees = $user->getEmployees();
        $page = $this->container->get('request')->query->get('page', 1);

        $adapter = new DoctrineCollectionAdapter($employees);
        $paginator = new Pagerfanta($adapter);
        $paginator->setMaxPerPage(15);
        $paginator->setCurrentPage($page);

        $entities = $paginator->getCurrentPageResults();

        return $this->render('OpenSkedgeBundle:User:index.html.twig', array(
            'displayonly' => true,
            'userstitle' => $userstitle,
            'entities' => $entities,
            'paginator' => $paginator,
            'emptymsg'    => $emptymsg,
        ));
    }

    /**
     * List a user's colleagues (those with the same supervisor).
     *
     * @param integer $id ID of user
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function colleaguesAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if (is_null($id)) {
            $user = $this->getUser();
            $userstitle = 'My Colleagues';
            $emptymsg = "You don't have any colleagues.";
        } else {
            $user = $em->getRepository('OpenSkedgeBundle:User')->find($id);

            if (!$user) {
                throw $this->createNotFoundException('Unable to find User.');
            }

            $userstitle = $user->getName()."'s Colleagues";
            $emptymsg = $user->getName()." doesn't appear to have any colleagues.";
        }

        $supervisors = $user->getSupervisors();
        $entities = array();
        foreach ($supervisors as $s) {
            $entities = array_merge($entities, $s->getEmployees()->filter(
                function ($entity) use ($user) {
                    return $entity->getId() != $user->getId();
                })->toArray());
        }

        $colleagues = array_unique($entities);

        $page = $this->container->get('request')->query->get('page', 1);

        $adapter = new ArrayAdapter($colleagues);
        $paginator = new Pagerfanta($adapter);
        $paginator->setMaxPerPage(15);
        $paginator->setCurrentPage($page);

        $entities = $paginator->getCurrentPageResults();

        return $this->render('OpenSkedgeBundle:User:index.html.twig', array(
            'displayonly' => true,
            'userstitle' => $userstitle,
            'entities' => $entities,
            'paginator' => $paginator,
            'emptymsg'    => $emptymsg,
        ));
    }

    /**
     * Lists all LateShift entites where the user missed their shift (never clocked in)
     *
     */
    public function missedShiftsAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if (is_null($id)) {
            $user = $this->getUser();
        } else {
            if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
                throw new AccessDeniedException();
            }
            $user = $em->getRepository('OpenSkedgeBundle:User')->find($id);

            if (!$user instanceof User) {
                throw $this->createNotFoundException('Unable to find User.');
            }
        }

        $missedShifts = $em->getRepository('OpenSkedgeBundle:LateShift')->findUserMissedShifts($user->getId());

        $page = $this->container->get('request')->query->get('page', 1);

        $adapter = new ArrayAdapter($missedShifts);
        $paginator = new Pagerfanta($adapter);
        $paginator->setMaxPerPage(15);
        $paginator->setCurrentPage($page);

        $entities = $paginator->getCurrentPageResults();

        if ($user->getId() == $this->getUser()->getId()) {
            return $this->render('OpenSkedgeBundle:LateShift:latemissed.html.twig', array(
                'entities'    => $entities,
                'paginator'   => $paginator,
                'title'       => "My Missing Shifts"
            ));
        } else {
            return $this->render('OpenSkedgeBundle:LateShift:index.html.twig', array(
                'entities'    => $entities,
                'paginator'   => $paginator,
                'title'       => $user->getName()."'s Missing Shifts"
            ));
        }
    }

    /**
     * Lists all LateShift entites where the user was late for their shift (but they did come in)
     *
     */
    public function lateShiftsAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if (is_null($id)) {
            $user = $this->getUser();
        } else {
            if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
                throw new AccessDeniedException();
            }
            $user = $em->getRepository('OpenSkedgeBundle:User')->find($id);

            if (!$user instanceof User) {
                throw $this->createNotFoundException('Unable to find User.');
            }
        }

        $lateShifts = $em->getRepository('OpenSkedgeBundle:LateShift')->findUserLateShifts($user->getId());

        $page = $this->container->get('request')->query->get('page', 1);

        $adapter = new ArrayAdapter($lateShifts);
        $paginator = new Pagerfanta($adapter);
        $paginator->setMaxPerPage(15);
        $paginator->setCurrentPage($page);

        $entities = $paginator->getCurrentPageResults();

        if ($user->getId() == $this->getUser()->getId()) {
            return $this->render('OpenSkedgeBundle:LateShift:latemissed.html.twig', array(
                'entities'    => $entities,
                'paginator'   => $paginator,
                'title'       => "My Late Shifts"
            ));
        } else {
            return $this->render('OpenSkedgeBundle:LateShift:index.html.twig', array(
                'entities'    => $entities,
                'paginator'   => $paginator,
                'title'       => $user->getName()."'s Late Shifts"
            ));
        }
    }

    /**
     * Ensure that any removed items collections actually get removed
     *
     * @param \Symfony\Component\Form\Form $form A Symfony form object
     *
     * @return void
     */
    protected function cleanupCollections(Form $form)
    {
        $children = $form->all();

        foreach ($children as $childForm) {
            $data = $childForm->getData();
            if ($data instanceof Collection) {

                // Get the child form objects and compare the data of each child against the object's current collection
                $proxies = $childForm->all();
                foreach ($proxies as $proxy) {
                    $entity = $proxy->getData();
                    if (!$data->contains($entity)) {
                        // Entity has been removed from the collection
                        $em = $this->getDoctrine()->getEntityManager();
                        $em->remove($entity);
                    }
                }
            }
        }
    }
}
