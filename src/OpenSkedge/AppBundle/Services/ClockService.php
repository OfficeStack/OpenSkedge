<?php

namespace OpenSkedge\AppBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\User\UserInterface;

use OpenSkedge\AppBundle\Entity\ArchivedClock;
use OpenSkedge\AppBundle\Entity\Clock;

class ClockService
{
    private $em;
    private $dtUtils;

    public function __construct(ObjectManager $em, DateTimeUtils $dtUtils)
    {
        $this->em = $em;
        $this->dtUtils = $dtUtils;
    }

    public function clockIn(UserInterface $user)
    {
        $clock = $user->getClock();

        $now = new \DateTime("now");
        $lastClockWeek = $this->dtUtils->getFirstDayOfWeek($clock->getLastClock(), true);
        $thisWeek = $this->dtUtils->getFirstDayOfWeek($now, true);

        // If the current user's clock entity is from before the current week, back it up and reset.
        if ($lastClockWeek->getTimestamp() < $thisWeek->getTimestamp()) {
            $archivedClock = $this->_backupClock($user, $clock);
            $clock->resetClock();
            $this->em->persist($archivedClock);
        }
        $clock->setStatus(true);
        $clock->setLastClock($now);

        $this->em->persist($clock);

        // Get a list of late shifts from today where the user has not arrived.
        $lateShifts = $this->em->getRepository('OpenSkedgeBundle:LateShift')
                               ->findUserLateShiftsToday($user->getId());
        // Get the time record index for the current time.
        $curIndex = $this->dtUtils->getIndexFromTime($now);
        // Get the day number from the current day.
        $dayNumber = $now->format("w");

        foreach ($lateShifts as $lateShift) {
            /* If one of the position schedules associated with one today's lateshifts is scheduled for now,
             * they are arriving for the shift and thus arrivalTime should be set to indicate they showed up
             * for the shift.
             */
            if ($lateShift->getSchedule()->getDayOffset($dayNumber, $curIndex) == '1') {
                $lateShift->setArrivalTime($now);
                $this->em->persist($lateShift);
            }
        }

        $this->em->flush();
    }

    public function clockOut(UserInterface $user)
    {
        $curTime = new \DateTime("now");
        $yesterday = clone $curTime;
        $yesterday->modify("-1 days");

        $clock = $user->getClock();

        $lastClock = $clock->getLastClock();
        $startTime = clone $lastClock;

        /* TODO: If they're clocking in over two pay periods. (e.g. Sunday night to monday morning)
         * we should update their archivedClock for that week.
         *
         * If the date of lastClock is the previous day, we need to update two timerecords.
         */
        if ($lastClock->format('Y-m-d') === $yesterday->format('Y-m-d')) {
            $prevDayEnd = clone $lastClock;
            $prevDayEnd->setTime(23, 59, 59);
            $day = $lastClock->format('w');
            $yesterdayTimerecord = $this->_updateTimeRecord($clock->getDay($day), $lastClock, $prevDayEnd);
            $clock->setDay($day, $yesterdayTimerecord);
            // The the final timerecord will be a continuation of midnight today until the current time.
            $startTime = new \DateTime("midnight today");
        } elseif ($lastClock->format('Y-m-d') < $yesterday->format('Y-m-d')) {
            /* They forgot to clock out over a number of days.
             * set $curTime to 23,59,59 on the last day the clocked in.
             * Not a perfect solution, but it minimizes the damage from not clocking out.
             */
            $curTime = clone $startTime;
            $curTime->setTime(23, 59, 59);
        }

        $day = $startTime->format('w');
        $curTimerecord = $clock->getDay($day);
        $timerecord = $this->_updateTimeRecord($curTimerecord, $startTime, $curTime);
        $clock->setDay($day, $timerecord);
        $clock->setStatus(false);

        $this->em->persist($clock);
        $this->em->flush();
    }

    /**
     * Backup a Clock entity to an ArchivedClock entity.
     *
     * @param UserInterface $user  The user of which's clock to manipulate.
     * @param Clock         $clock The user's Clock entity
     *
     * @return ArchivedClock
     */
    private function _backupClock(UserInterface $user, Clock $clock)
    {
        $archivedClock = new ArchivedClock();
        $archivedClock->setUser($user);

        for ($i = 0; $i < 7; $i++) {
            $archivedClock->setDay($i, $clock->getDay($i));
        }
        $archivedClock->setWeek($this->dtUtils->getFirstDayOfWeek($clock->getLastClock()));

        return $archivedClock;
    }

    /**
     * Set 1s in the given timerecord 96-character string from $start to $end
     *
     * @param string    $timerecord 96-character time record string
     * @param \DateTime $start      Start time
     * @param \DateTime $end        End time
     *
     * @return string
     */
    private function _updateTimeRecord($timerecord, \DateTime $start, \DateTime $end)
    {
        // Get a \DateTime for midnight of $start's day.
        $daystart = clone $start;
        $daystart->setTime(0, 0, 0);

        // Time record index of the start time (exclusive)
        $startInd = round((($start->getTimestamp() - $daystart->getTimestamp()) / 60) / 15);
        // Time record index of the end time (inclusive)
        $endInd = round((($end->getTimestamp() - $daystart->getTimestamp()) / 60) / 15);

        for ($i = $startInd; $i < $endInd; $i++) {
            $timerecord[$i] = '1';
        }

        return $timerecord;
    }
}
