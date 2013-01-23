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

        $user = $this->getUser();

        $selected = $request->request->get('schedulePeriod', 0);

        $results = $em->createQuery('SELECT sp, s, a FROM OpenSkedgeBundle:SchedulePeriod sp
                                    LEFT JOIN sp.schedules s JOIN sp.availabilitySchedules a
                                    WHERE (sp.startTime <= CURRENT_TIMESTAMP()
                                    AND sp.endTime >= CURRENT_TIMESTAMP() AND s.schedulePeriod = sp.id
                                    AND a.schedulePeriod = sp.id AND s.user = :uid AND a.user = :uid)
                                    ORDER BY sp.endTime DESC')
                      ->setParameter('uid', $user->getId())
                      ->getResult();

        $avails = $results[$selected]->getAvailabilitySchedules();
        $avail = $avails[0];

        if(in_array($request->getClientIp(), $this->container->getParameter('allowed_clock_ips'))) {
            $outside = false;
        } else {
            $outside = true;
        }

        return $this->render('OpenSkedgeBundle:Dashboard:index.html.twig', array(
            'htime'           => mktime(0,0,0,1,1),
            'resolution'      => "1 hour",
            'avail'           => $avail,
            'schedulePeriods' => $results,
            'schedules'       => $results[$selected]->getSchedules(),
            'selected'        => $selected,
            'outside'         => $outside,
        ));
    }
}
