<?php

namespace OpenSkedge\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use OpenSkedge\AppBundle\Entity\AvailabilitySchedule;
use OpenSkedge\AppBundle\Entity\Schedule;

/**
 * Statistics controller
 *
 * @category Controller
 * @package  OpenSkedge\AppBundle\Controller
 * @author   Max Fierke <max@maxfierke.com>
 * @license  GNU General Public License, version 3
 * @version  GIT: $Id$
 * @link     https://github.com/maxfierke/OpenSkedge OpenSkedge Github
 */
class StatsController extends Controller
{
    public function indexAction(Request $request, $id)
    {
        if (is_null($id)) {
            $id = $this->getUser()->getId();
        }

        $stats = $this->get('stats');

        $selected = $request->request->get('schedulePeriod', 0);

        $em = $this->getDoctrine()->getManager();

        $schedulePeriods = $em->getRepository('OpenSkedgeBundle:SchedulePeriod')->findUserSchedulePeriods($id);

        $composite = '';
        $lmLabels = array();
        $lateCounts = array();
        $missedCounts = array();
        $hoursAvailable = 0;
        $hoursScheduled = 0;
        if (count($schedulePeriods) > 0) {
            $start = $schedulePeriods[$selected]->getStartTime();
            $end = $schedulePeriods[$selected]->getEndTime();
            $spPeriod = new \DatePeriod($start, new \DateInterval("P1W"), $end);
            $monthPeriod = new \DatePeriod($start, new \DateInterval("P1M"), $end);
            foreach ($spPeriod as $period) {
                $weekClockReport = $stats->weekClockReport($id, $period);
                if ($weekClockReport instanceof Schedule) {
                    for ($i = 0; $i < 7; $i++) {
                        $composite .= $weekClockReport->getDay($i);
                    }
                }
            }
            foreach ($monthPeriod as $period) {
                $nextPeriod = clone $period;
                $nextPeriod->add(new \DateInterval("P1M"));
                $lateCount[] = $em->getRepository('OpenSkedgeBundle:LateShift')
                                  ->getUserLateShiftCount($id, $period, $nextPeriod);
                $missedCounts[] = $em->getRepository('OpenSkedgeBundle:LateShift')
                                  ->getUserMissedShiftCount($id, $period, $nextPeriod);
                $lmLabels[] = $period->format('F');
            }

            $entity = $em->getRepository('OpenSkedgeBundle:AvailabilitySchedule')->findOneBy(array(
                'user' => $id,
                'schedulePeriod' => $schedulePeriods[$selected]
            ));

            if ($entity instanceof AvailabilitySchedule) {
                for ($day = 0; $day < 7; $day++) {
                    $timeRec = $entity->getDay($day);
                    $hoursAvailable += substr_count($timeRec, '1') + substr_count($timeRec, '2') + substr_count($timeRec, '3');
                }
                $hoursAvailable = $hoursAvailable / 4;

                $schedules = $em->getRepository('OpenSkedgeBundle:Schedule')->findBy(array(
                    'user' => $id,
                    'schedulePeriod' => $schedulePeriods[$selected]
                ));

                foreach($schedules as $schedule) {
                    $scheduleSum = 0;
                    for ($day = 0; $day < 7; $day++) {
                        $scheduleSum += substr_count($schedule->getDay($day), '1');
                    }
                    $hoursScheduled += $scheduleSum / 4;
                }
            }
        }

        $notScheduledNotWorked = new \stdClass;
        $notScheduledNotWorked->value = substr_count($composite, '0');
        $notScheduledNotWorked->color = "#BBbbBB";

        $scheduledWorked = new \stdClass;
        $scheduledWorked->value = substr_count($composite, '1');
        $scheduledWorked->color = "#008000";

        $scheduledNotWorked = new \stdClass;
        $scheduledNotWorked->value = substr_count($composite, '2');
        $scheduledNotWorked->color = "#DC143C";

        $workedNotScheduled = new \stdClass;
        $workedNotScheduled->value = substr_count($composite, '3');
        $workedNotScheduled->color = "#FFcc00";

        $hoursData = array(
            $notScheduledNotWorked,
            $scheduledWorked,
            $scheduledNotWorked,
            $workedNotScheduled
        );

        $lateObj = new \stdClass;
        $lateObj->fillColor = "rgba(220,220,220,0.5)";
        $lateObj->strokeColor = "rgba(220,220,220,1)";
        $lateObj->pointColor = "rgba(220,220,220,1)";
        $lateObj->pointStrokeColor = "#fff";
        $lateObj->data = $lateCounts;

        $missedObj = new \stdClass;
        $missedObj->fillColor = "rgba(151,187,205,0.5)";
        $missedObj->strokeColor = "rgba(151,187,205,1)";
        $missedObj->pointColor = "rgba(151,187,205,1)";
        $missedObj->pointStrokeColor = "#fff";
        $missedObj->data = $missedCounts;

        $lmData = new \stdClass;
        $lmData->labels = $lmLabels;
        $lmData->datasets = array($lateObj, $missedObj);

        $availHours = new \stdClass;
        $availHours->value = $hoursAvailable;
        $availHours->color = "#69D2E7";

        $schHours = new \stdClass;
        $schHours->value = $hoursAvailable - $hoursScheduled;
        $schHours->color = "#F38630";
        $schData = array($schHours, $availHours);

        return $this->render('OpenSkedgeBundle:Stats:stats.html.twig', array(
            'schedulePeriods' => $schedulePeriods,
            'selected'        => $selected,
            'hoursData'       => json_encode($hoursData),
            'lmData'          => json_encode($lmData),
            'schData'         => json_encode($schData)
        ));
    }
}
