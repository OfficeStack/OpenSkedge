<?php

namespace OpenSkedge\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Controller for OpenSkedge Dashboard
 *
 * @category Controller
 * @package  OpenSkedge\AppBundle\Controller
 * @author   Max Fierke <max@maxfierke.com>
 * @license  GNU General Public License, version 3
 * @version  GIT: $Id$
 * @link     https://github.com/maxfierke/OpenSkedge OpenSkedge Github
 */
class DashboardController extends Controller
{
    /**
     * Display the user's current schedules, time clock, and any other additional modules.
     *
     * @param Request $request The user's request object
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        $appSettings = $this->get('app_settings')->getAppSettings();

        $selected = $request->request->get('schedulePeriod', 0);

        // Get the requested time resolution, if available. Default to global default.
        $resolution = $request->request->get('timeresolution', $appSettings->getDefaultTimeResolution());

        /* Get schedules periods and their associated availability schedules and position schedules
         * ordered by the end time of each scheduling period (descending)
         */
        $results = $em->createQuery('SELECT sp, s, a FROM OpenSkedgeBundle:SchedulePeriod sp
                                    LEFT JOIN sp.schedules s JOIN sp.availabilitySchedules a
                                    WHERE (sp.startTime <= CURRENT_TIMESTAMP()
                                    AND sp.endTime >= CURRENT_TIMESTAMP() AND s.schedulePeriod = sp.id
                                    AND a.schedulePeriod = sp.id AND s.user = :uid AND a.user = :uid)
                                    ORDER BY sp.endTime DESC')
            ->setParameter('uid', $user->getId())
            ->getResult();

        // If we've got results, populate the relevant arrays.
        if (count($results) > 0) {
            $avails = $results[$selected]->getAvailabilitySchedules();
            $schedules = $results[$selected]->getSchedules();
            $avail = $avails[0];
        } else {
            $avail = null;
            $schedules = null;
        }

        /* If running on Pagoda Box, get the user's IP directly from HTTP_X_FORWARDED_FOR,
         * otherwise, go to Request::getClientIp()
         */
        $clientIp = (isset($_ENV['PAGODABOX']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $request->getClientIp());
        if (in_array($clientIp, $this->get('app_settings')->getAllowedClockIps())) {
            $outside = false;
        } else {
            $outside = true;
        }

        $dtUtils = $this->get('dt_utils');

        // Get the indices of of the globally defined schedule start & end hours
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

    /**
     * Lists all Shift entites the user has picked up (if within the current week)
     * and the intervals the user is scheduled.
     *
     */
    public function shiftsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        $dtUtils = $this->get('dt_utils');
        $weekStart = $dtUtils->getFirstDayOfWeek(new \DateTime());
        $weekEnd = clone $weekStart;
        $weekEnd->modify('+7 day');

        $week = new \DatePeriod($weekStart, new \DateInterval("P1D"), $weekEnd);

        $schedulePeriods = $em->createQuery('SELECT sp FROM OpenSkedgeBundle:SchedulePeriod sp
            WHERE (sp.startTime <= CURRENT_TIMESTAMP() AND sp.endTime >= CURRENT_TIMESTAMP()) ORDER BY sp.startTime, sp.endTime ASC')
            ->getResult();

        $shifts = array();
        $posintervals = array();
        foreach ($schedulePeriods as $schedulePeriod) {
            $tempShifts = $em->createQuery('SELECT shift FROM OpenSkedgeBundle:Shift shift
                    WHERE (shift.startTime >= :ws AND shift.startTime <= :we AND shift.pickedUpBy = :uid AND shift.schedulePeriod = :spid AND shift.status != \'unapproved\') ORDER BY shift.startTime ASC')
                ->setParameter('ws', $weekStart)
                ->setParameter('we', $weekEnd)
                ->setParameter('uid', $user->getId())
                ->setParameter('spid', $schedulePeriod->getId())
                ->getResult();
            $shifts += $tempShifts;

            $schedules = $em->createQuery('SELECT s FROM OpenSkedgeBundle:Schedule s
                    WHERE (s.schedulePeriod = :spid AND s.user = :uid)')
                ->setParameter('uid', $user->getId())
                ->setParameter('spid', $schedulePeriod->getId())
                ->getResult();

            $intervals = array();
            foreach ($schedules as $schedule) {
                $interval            = new \stdClass;
                $interval->position  = $schedule->getPosition();
                $interval->sunday    = $dtUtils->getDateTimeIntervals($schedule->getSun(), 0);
                $interval->monday    = $dtUtils->getDateTimeIntervals($schedule->getMon(), 1);
                $interval->tuesday   = $dtUtils->getDateTimeIntervals($schedule->getTue(), 2);
                $interval->wednesday = $dtUtils->getDateTimeIntervals($schedule->getWed(), 3);
                $interval->thursday  = $dtUtils->getDateTimeIntervals($schedule->getThu(), 4);
                $interval->friday    = $dtUtils->getDateTimeIntervals($schedule->getFri(), 5);
                $interval->saturday  = $dtUtils->getDateTimeIntervals($schedule->getSat(), 6);
                $intervals[]         = $interval;
            }
            $posintervals[] = $intervals;
        }

        return $this->render('OpenSkedgeBundle:Dashboard:shifts.html.twig', array(
            'shifts'          => $shifts,
            'schedulePeriods' => $schedulePeriods,
            'intervals'       => $posintervals,
            'week'            => $week
        ));
    }
}
