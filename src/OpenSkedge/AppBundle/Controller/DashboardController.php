<?php

namespace OpenSkedge\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DashboardController extends Controller
{
    public function indexAction(Request $request)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        $qb = $em->createQueryBuilder();

        /* TODO: Allow multiple availability schedules to be passed if there are multiple schedule periods active.
         *
         */
        $user =  $this->getUser();

        if(empty($schedulePeriod)) {
            try {
                $schedulePeriod = $qb->select('sp')
                                      ->from('OpenSkedgeBundle:SchedulePeriod', 'sp')
                                      ->where('sp.startTime < CURRENT_TIMESTAMP()')
                                      ->andWhere('sp.endTime > CURRENT_TIMESTAMP()')
                                      ->orderBy('sp.endTime', 'DESC')
                                      ->getQuery()
                                      ->setMaxResults(1)
                                      ->getSingleResult();
                $avail = $em->getRepository('OpenSkedgeBundle:AvailabilitySchedule')->findOneBy(array('schedulePeriod' => $schedulePeriod->getId(), 'user' => $user->getId()));
                $schedules = $em->getRepository('OpenSkedgeBundle:Schedule')->findBy(array('schedulePeriod' => $schedulePeriod->getId(), 'user' => $user->getId()));
            } catch (\Doctrine\ORM\NoResultException $e){
                // It's cool, homie.
                $avail = null;
                $schedulePeriod = null;
                $schedules = array();
            }
        }
        if(in_array($request->getClientIp(), $this->container->getParameter('allowed_clock_ips'))) {
            $outside = false;
        } else {
            $outside = true;
        }

        return $this->render('OpenSkedgeBundle:Dashboard:index.html.twig', array(
            'htime'      => mktime(0,0,0,1,1),
            'resolution' => "1 hour",
            'avail'      => $avail,
            'schedules'  => $schedules,
            'outside'    => $outside,
        ));
    }
}
