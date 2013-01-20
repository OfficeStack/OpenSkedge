<?php
namespace OpenSkedge\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use OpenSkedge\AppBundle\Entity\ArchivedClock;
use OpenSkedge\AppBundle\Entity\Clock;
use OpenSkedge\AppBundle\Entity\User;

/**
 * Clock controller.
 *
 */
class ClockController extends Controller
{
    public function checklateAction(Request $request)
    {
        if($request->getMethod() == 'POST') {
            $token = $this->container->getParameter('kernel.secret');
            if($request->request->get('clockToken')===$token) {
                // TODO: Check for users who have not clocked in and are 15 mins late or more.
            }
        }
    }

    public function clockInAction(Request $request)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }

        if(!in_array($request->getClientIp(), array('127.0.0.1', '::1'))) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();
        $clock = $user->getClock();

        $now = new \DateTime("now");
        $lastClockWeek = $this->getFirstDayOfWeek($clock->getLastClock());
        $thisWeek = $this->getFirstDayOfWeek($now);
        if($lastClockWeek < $thisWeek) {
            $archivedClock = $this->backupClock($user, $clock);
            $clock->resetClock();
            $em->persist($archivedClock);
        }
        $clock->setStatus(true);
        $clock->setLastClock($now);

        $em->persist($clock);
        $em->flush();
        /*$return = array("responseCode" => 200);
        $return = json_encode($return);
        return new Response($return, 200, array('Content-Type'=>'application/json'));*/
        return $this->redirect($this->generateUrl('dashboard'));
    }

    // TODO: Add late checking
    // TODO: Add shift change checking
    public function clockOutAction(Request $request)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }

        if(!in_array($request->getClientIp(), array('127.0.0.1', '::1'))) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        $cur_time = new \DateTime("now");

        $user = $this->getUser();
        $clock = $user->getClock();

        $last_clock = $clock->getLastClock();
        $start_time = $last_clock;

        /* TODO: If they're clocking in over two pay periods. (e.g. Sunday night to monday morning)
         * we should update their archivedClock for that week.
         *
         * If the date of last_clock is the previous day, we need to update two timerecords.
         */
        if($last_clock->format('w') == $cur_time->format('w')-1) {
            $prev_day_end = mktime(23, 59, 59, $last_clock->format('n'), $last_clock->format('j'), $last_clock->format('Y'));
            $getDay = "get".$last_clock->format('D');
            $yesterday_timerecord = static::updateTimeRecord($clock->$getDay(), $last_clock->getTimestamp(), $prev_day_end);
            $setDay = "set".$last_clock->format('D');
            $clock->$setDay($yesterday_timerecord);
            // The the final timerecord will be a continuation of midnight today until the current time.
            $start_time = mktime(0, 0, 0);
        }

        $getDay = "get".date('D');
        $setDay = "set".date('D');
        $cur_timerecord = $clock->$getDay();
        $timerecord = self::updateTimeRecord($cur_timerecord, $start_time->getTimestamp(), $cur_time->getTimestamp());
        $clock->$setDay($timerecord);
        $clock->setStatus(false);

        $em->persist($clock);
        $em->flush();

        /*$return = array("responseCode" => 200);
        $return = json_encode($return);
        return new Response($return, 200, array('Content-Type'=>'application/json'));*/
        return $this->redirect($this->generateUrl('dashboard'));
    }

    private function backupClock($user, $clock)
    {
        $archivedClock = new ArchivedClock();
        $archivedClock->setUser($user);
        for($i = 0; $i < 7; $i++) {
            $archivedClock->setDay($i, $clock->getDay($i));
        }
        $archivedClock->setWeek($this->getFirstDayOfWeek($clock->getLastClock()));
        return $archivedClock;
    }

    /**
     * @param \DateTime $date A given date
     * @return \DateTime
     */
    private function getFirstDayOfWeek(\DateTime $date) {
        $day = $this->container->getParameter('week_start_day_clock');
        $firstDay = idate('w', strtotime($day));
        $offset = 7 - $firstDay;
        $ret = clone $date;
        $ret->modify(-(($date->format('w') + $offset) % 7) . 'days');
        return $ret;
    }

    private static function updateTimeRecord($timerecord, $start, $end)
    {
        // Get the UNIX timestamp for midnight.
        $daystart = mktime(0, 0, 0);
        // Time record index of the start time (inclusive)
        $startInd = round((($start - $daystart) / 60) / 15)-1;
        // Time record index of the end time (exclusive)
        $endInd = round((($end - $daystart) / 60) / 15);
        for($i=$startInd; $i < $endInd; $i++)
        {
            $timerecord[$i] = '1';
        }
        return $timerecord;
    }
}
