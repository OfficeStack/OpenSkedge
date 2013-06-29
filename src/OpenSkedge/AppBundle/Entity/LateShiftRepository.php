<?php

namespace OpenSkedge\AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class LateShiftRepository extends EntityRepository
{
    public function findUserLateShiftsToday($uid)
    {
        return $this->getEntityManager()->createQuery('SELECT DISTINCT ls FROM OpenSkedgeBundle:LateShift ls
            WHERE (ls.arrivalTime IS NULL AND DATE_DIFF(CURRENT_DATE(), ls.creationTime) = 0 AND ls.user = :uid)
            ORDER BY ls.creationTime DESC')
            ->setParameter('uid', $uid)
            ->getResult();
    }

    public function findLateShiftsTodaybySchedule($sid)
    {
        return $this->getEntityManager()->createQuery('SELECT DISTINCT ls FROM OpenSkedgeBundle:LateShift ls
            WHERE (ls.arrivalTime IS NULL AND DATE_DIFF(CURRENT_DATE(), ls.creationTime) = 0
            AND ls.schedule = :sid) ORDER BY ls.creationTime DESC')
            ->setParameter('sid', $sid)
            ->getResult();
    }

    public function findUserLateShifts($uid, $start = null, $end = null)
    {
        if ($start === null and $end === null) {
            return $this->getEntityManager()->createQuery('SELECT ls FROM OpenSkedgeBundle:LateShift ls
                WHERE (ls.arrivalTime IS NOT NULL AND ls.user = :uid) ORDER BY ls.creationTime DESC')
                ->setParameter('uid', $uid)
                ->getResult();
        } else {
            return $this->getEntityManager()->createQuery('SELECT ls FROM OpenSkedgeBundle:LateShift ls
                WHERE (ls.creationTime >= :start AND ls.creationTime < :end AND ls.arrivalTime IS NOT NULL
                AND ls.user = :uid)' )
                ->setParameter('uid', $uid)
                ->setParameter('start', $start)
                ->setParameter('end', $end)
                ->getResult();
        }
    }

    public function findUserMissedShifts($uid, $start = null, $end = null)
    {
        if ($start === null and $end === null) {
            return $this->getEntityManager()->createQuery('SELECT ls FROM OpenSkedgeBundle:LateShift ls
                WHERE (ls.arrivalTime IS NULL AND ls.user = :uid) ORDER BY ls.creationTime DESC')
                ->setParameter('uid', $uid)
                ->getResult();
        } else {
            return $this->getEntityManager()->createQuery('SELECT ls FROM OpenSkedgeBundle:LateShift ls
                WHERE (ls.creationTime >= :start AND ls.creationTime < :end AND ls.arrivalTime IS NULL
                AND ls.user = :uid) ORDER BY ls.creationTime DESC')
                ->setParameter('uid', $uid)
                ->setParameter('start', $start)
                ->setParameter('end', $end)
                ->getResult();
        }
    }

    public function getUserLateShiftCount($uid, $start = null, $end = null)
    {
        if ($start === null and $end === null) {
            return $this->getEntityManager()->createQuery('SELECT COUNT(ls.id) FROM OpenSkedgeBundle:LateShift ls
                WHERE (ls.arrivalTime IS NOT NULL AND ls.user = :uid)')
                ->setParameter('uid', $uid)
                ->getSingleScalarResult();
        } else {
            return $this->getEntityManager()->createQuery('SELECT COUNT(ls.id) FROM OpenSkedgeBundle:LateShift ls
                WHERE (ls.creationTime >= :start AND ls.creationTime < :end AND ls.arrivalTime IS NOT NULL
                AND ls.user = :uid)')
                ->setParameter('uid', $uid)
                ->setParameter('start', $start)
                ->setParameter('end', $end)
                ->getSingleScalarResult();
        }
    }

    public function getUserMissedShiftCount($uid, $start = null, $end = null)
    {
        if ($start === null and $end === null) {
            return $this->getEntityManager()->createQuery('SELECT COUNT(ls.id) FROM OpenSkedgeBundle:LateShift ls
                WHERE (ls.arrivalTime IS NULL AND ls.user = :uid)')
                ->setParameter('uid', $uid)
                ->getSingleScalarResult();
        } else {
            return $this->getEntityManager()->createQuery('SELECT COUNT(ls.id) FROM OpenSkedgeBundle:LateShift ls
                WHERE (ls.creationTime >= :start AND ls.creationTime < :end AND ls.arrivalTime IS NULL
                AND ls.user = :uid)')
                ->setParameter('uid', $uid)
                ->setParameter('start', $start)
                ->setParameter('end', $end)
                ->getSingleScalarResult();
        }
    }

    public function supportsClass($class)
    {
        return $this->getEntityName() === $class || is_subclass_of($class, $this->getEntityName());
    }
}
