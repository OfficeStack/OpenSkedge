<?php

namespace OpenSkedge\AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class SchedulePeriodRepository extends EntityRepository
{

    /**
     * Find Schedule Periods that are currently ongoing.
     *
     * @return DoctrineCollection
     */
    public function findCurrentSchedulePeriods()
    {
        return $this->getEntityManager()->createQuery('SELECT sp FROM OpenSkedgeBundle:SchedulePeriod sp
            WHERE (sp.startTime <= CURRENT_TIMESTAMP() AND sp.endTime >= CURRENT_TIMESTAMP())
            ORDER BY sp.startTime, sp.endTime ASC')
            ->getResult();
    }

    public function findSchedulePeriodsForWeek(\DateTime $week)
    {
        return $this->createQueryBuilder('sp')
            ->select('sp')
            ->where('sp.startTime <= :week')
            ->andWhere('sp.endTime >= :week')
            ->setParameter('week', $week)
            ->getQuery()
            ->getResult();
    }

    /**
     * Get schedules periods and their associated availability schedules and position schedules
     * ordered by the end time of each scheduling period (descending)
     *
     * @param integer $uid A user ID
     *
     * @return DoctrineCollection
     */
    public function findUserSchedulePeriodsAssoc($uid)
    {
        return $this->getEntityManager()->createQuery('SELECT sp, s, a FROM OpenSkedgeBundle:SchedulePeriod sp
            LEFT JOIN sp.schedules s JOIN sp.availabilitySchedules a
            WHERE (sp.startTime <= CURRENT_TIMESTAMP() AND sp.endTime >= CURRENT_TIMESTAMP()
            AND s.schedulePeriod = sp.id AND a.schedulePeriod = sp.id AND s.user = :uid
            AND a.user = :uid) ORDER BY sp.endTime DESC')
            ->setParameter('uid', $uid)
            ->getResult();
    }

    /**
     * Get user's schedules periods for which they have availability schedules and position schedules
     *
     * @param integer $uid A user ID
     *
     * @return DoctrineCollection
     */
    public function findUserSchedulePeriods($uid)
    {
        return $this->getEntityManager()->createQuery('SELECT sp FROM OpenSkedgeBundle:SchedulePeriod sp
                LEFT JOIN sp.schedules s JOIN sp.availabilitySchedules a
                WHERE (s.schedulePeriod = sp.id AND a.schedulePeriod = sp.id AND s.user = :uid
                AND a.user = :uid) ORDER BY sp.endTime DESC')
            ->setParameter('uid', $uid)
            ->getResult();
    }

    public function supportsClass($class)
    {
        return $this->getEntityName() === $class || is_subclass_of($class, $this->getEntityName());
    }
}
