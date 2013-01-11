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
        $user =  $this->getUser();

        try {
            $schedulePeriod = $qb->select('sp')
                                  ->from('FlexSchedBundle:SchedulePeriod', 'sp')
                                  ->where('sp.startTime < CURRENT_TIMESTAMP()')
                                  ->andWhere('sp.endTime > CURRENT_TIMESTAMP()')
                                  ->orderBy('sp.endTime', 'DESC')
                                  ->getQuery()
                                  ->setMaxResults(1)
                                  ->getSingleResult();
            $avail = $em->getRepository('FlexSchedBundle:AvailabilitySchedule')->findOneBy(array('schedulePeriod' => $schedulePeriod->getId(), 'user' => $user->getId()));
        } catch (\Doctrine\ORM\NoResultException $e){
            // It's cool, homie.
            $avail = null;
        }

        $schedules = $em->getRepository('FlexSchedBundle:Schedule')->findBy(array('schedulePeriod' => $schedulePeriod->getId(), 'user' => $user->getId()));

        return $this->render('FlexSchedBundle:Dashboard:index.html.twig', array(
            'htime'     => mktime(0,0,0,1,1),
            'resolution' => "1 hour",
            'avail'       => $avail,
            'schedules'  => $schedules,
        ));
    }
}
