<?php

namespace FlexSched\SchedBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

class DashboardController extends Controller
{
    public function indexAction()
    {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        $qb = $em->createQueryBuilder();

        /* TODO: Allow multiple availability schedules to be passed if there are multiple schedule periods active.
         *
         */
        try {
            $schedulePeriod = $qb->select('sp')
                                  ->from('FlexSchedBundle:SchedulePeriod', 'sp')
                                  ->where('sp.startTime < CURRENT_TIMESTAMP()')
                                  ->andWhere('sp.endTime > CURRENT_TIMESTAMP()')
                                  ->orderBy('sp.endTime', 'DESC')
                                  ->getQuery()
                                  ->setMaxResults(1)
                                  ->getSingleResult();
            $user = $this->getUser();
            $avail = $qb->select('a')
                        ->from('FlexSchedBundle:AvailabilitySchedule', 'a')
                        ->where('a.schedulePeriod = :schedulePeriod')
                        ->andWhere('a.user = :user')
                        ->setParameter('schedulePeriod', $schedulePeriod)
                        ->setParameter(':user', $user)
                        ->getQuery()
                        ->setMaxResults(1)
                        ->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e){
            // It's cool, homie.
            $avail = null;
        }

        $schedules = $em->createQueryBuilder('s')
                        ->select('s')
                        ->from('FlexSchedBundle:Schedule', 's')
                        ->where('s.schedulePeriod = :sp')
                        ->andWhere('s.user = :user')
                        ->setParameters(array('sp' => $schedulePeriod, 'user' => $user))
                        ->getQuery()
                        ->setMaxResults(5)
                        ->getResult();

        return $this->render('FlexSchedBundle:Dashboard:index.html.twig', array(
            'htime'     => mktime(0,0,0,1,1),
            'resolution' => "1 hour",
            'avail'       => $avail,
            'schedules'  => $schedules,
        ));
    }
}
