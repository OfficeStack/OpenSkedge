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

        $appSettings = $this->get('appsettings')->getAppSettings();

        $selected = $request->request->get('schedulePeriod', 0);

        $resolution = $request->request->get('timeresolution', $appSettings->getDefaultTimeResolution());

        $results = $em->createQuery('SELECT sp, s, a FROM OpenSkedgeBundle:SchedulePeriod sp
                                    LEFT JOIN sp.schedules s JOIN sp.availabilitySchedules a
                                    WHERE (sp.startTime <= CURRENT_TIMESTAMP()
                                    AND sp.endTime >= CURRENT_TIMESTAMP() AND s.schedulePeriod = sp.id
                                    AND a.schedulePeriod = sp.id AND s.user = :uid AND a.user = :uid)
                                    ORDER BY sp.endTime DESC')
                      ->setParameter('uid', $user->getId())
                      ->getResult();

        if (count($results) > 0) {
            $avails = $results[$selected]->getAvailabilitySchedules();
            $schedules = $results[$selected]->getSchedules();
            $avail = $avails[0];
        } else {
            $avail = null;
            $schedules = null;
        }

        $clientIp = (isset($_ENV['PAGODABOX']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $request->getClientIp());
        if (in_array($clientIp, $this->get('appsettings')->getAllowedClockIps())) {
            $outside = false;
        } else {
            $outside = true;
        }

        $dtUtils = $this->get('dt_utils');

        $startIndex = $dtUtils->getIndexFromTime($appSettings->getStartHour());
        $endIndex = $dtUtils->getIndexFromTime($appSettings->getEndHour())-1;

        return $this->render('OpenSkedgeBundle:Dashboard:index.html.twig', array(
            'htime'           => $dtUtils->timeStrToDateTime($appSettings->getStartHour()),
            'resolution'      => $resolution,
            'avail'           => $avail,
            'schedulePeriods' => $results,
            'schedules'       => $schedules,
            'selected'        => $selected,
            'outside'         => $outside,
            'clientip'        => $clientIp,
            'startIndex'      => $startIndex,
            'endIndex'        => $endIndex
        ));
    }
}
