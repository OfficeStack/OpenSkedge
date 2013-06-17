<?php

namespace OpenSkedge\AppBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;

use OpenSkedge\AppBundle\Entity\ArchivedClock;
use OpenSkedge\AppBundle\Entity\Clock;
use OpenSkedge\AppBundle\Entity\Schedule;
use OpenSkedge\AppBundle\Entity\User;

class StatsService
{
    private $em;
    private $dtUtils;

    public function __construct(ObjectManager $em, DateTimeUtils $dtUtils)
    {
        $this->em = $em;
        $this->dtUtils = $dtUtils;
    }

    public function weekClockReport($uid, $week)
    {
        $week = $this->dtUtils->getFirstDayOfWeek($week);
        $nextWeek = clone $week;
        $nextWeek->modify("+7 days");

        $user = $this->em->getRepository('OpenSkedgeBundle:User')->find($uid);

        if (!$user instanceof User) {
            throw new \Exception('User entity was not found!');
        }

        $archivedClock = $this->em->getRepository('OpenSkedgeBundle:ArchivedClock')->findOneBy(array('week' => $week, 'user' => $uid));

        if (!$archivedClock instanceof ArchivedClock) {
            return new Schedule();
        }

        $schedulePeriods = $this->em->createQueryBuilder()
            ->select('sp')
            ->from('OpenSkedgeBundle:SchedulePeriod', 'sp')
            ->where('sp.startTime <= :week')
            ->andWhere('sp.endTime >= :week')
            ->setParameter('week', $week)
            ->getQuery()
            ->getResult();

        $schedules = array();
        foreach ($schedulePeriods as $sp) {
            $schedules[] = $this->em->getRepository('OpenSkedgeBundle:Schedule')->findBy(array('user' => $uid, 'schedulePeriod' => $sp->getId()));
        }

        // Create a temporary Schedule entity to store the composite of all the users schedules.
        $scheduled = new Schedule();
        for ($i = 0; $i < count($schedulePeriods); $i++) {
            foreach ($schedules[$i] as $s) {
                for ($timesect = 0; $timesect < 96; $timesect++) {
                    for ($day = 0; $day < 7; $day++) {
                        if ($s->getDayOffset($day, $timesect) == '1') {
                                $scheduled->setDayOffset($day, $timesect, '1');
                        }
                    }
                }
            }
        }

        $shifts = $this->em->createQuery("SELECT sh FROM OpenSkedgeBundle:Shift sh
            WHERE (sh.pickedUpBy = :user AND sh.startTime >= :week AND sh.endTime < :nextWeek AND sh.status != 'unapproved')")
            ->setParameter('user', $user)
            ->setParameter('week', $week)
            ->setParameter('nextWeek', $nextWeek)
            ->getResult();

        foreach ($shifts as $shift) {
            $startIndex = $this->dtUtils->getIndexFromTime($shift->getStartTime());
            $endIndex = $this->dtUtils->getIndexFromTime($shift->getEndTime());
            // Update the schedule composite to mark shifts they had picked up.
            if ($shift->getStartTime()->format('w') === $shift->getEndTime()->format('w')) {
                $day = $shift->getStartTime()->format('w');
                for ($i = $startIndex; $i < $endIndex; $i++) {
                    $scheduled->setDayOffset($day, $i, '1');
                }
            } else if ($shift->getStartTime()->format('w') === $shift->getEndTime()->format('w')-1) {
                $day = $shift->getStartTime()->format('w');
                for ($i = $startIndex; $i < 96; $i++) {
                    $scheduled->setDayOffset($day, $i, '1');
                }
                for ($i = 0; $i < $endIndex; $i++) {
                    $scheduled->setDayOffset($day+1, $i, '1');
                }
            }
        }

        for ($i = 0; $i < 7; $i++) {
            for ($j = 0; $j < 96; $j++) {
                if ($archivedClock->getDayOffset($i, $j) == '1' and $scheduled->getDayOffset($i, $j) == '1') {
                    $scheduled->setDayOffset($i, $j, '1');
                } elseif ($archivedClock->getDayOffset($i, $j) == '1' and $scheduled->getDayOffset($i, $j) == '0') {
                    $scheduled->setDayOffset($i, $j, '3');
                } elseif ($archivedClock->getDayOffset($i, $j) == '0' and $scheduled->getDayOffset($i, $j) == '1') {
                    $scheduled->setDayOffset($i, $j, '2');
                } else {
                    $scheduled->setDayOffset($i, $j, '0');
                }
            }
        }

        return $scheduled;
    }
}
