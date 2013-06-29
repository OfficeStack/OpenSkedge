<?php

namespace OpenSkedge\AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ShiftRepository extends EntityRepository
{
    /**
     * Find user shifts within interval
     *
     * @param integer  $uid           User ID
     * @param integer  $spid          Schedule period ID
     * @param DateTime $intervalStart DateTime of interval start
     * @param DateTime $intervalEnd   DateTime of interval end
     *
     * @return DoctrineCollection
     */
    public function findUserShiftsInInterval($uid, $spid, $intervalStart, $intervalEnd)
    {
        return $this->getEntityManager()
            ->createQuery('SELECT shift FROM OpenSkedgeBundle:Shift shift
                WHERE (shift.startTime >= :is AND shift.startTime <= :ie AND shift.pickedUpBy = :uid AND shift.schedulePeriod = :spid AND shift.status != \'unapproved\') ORDER BY shift.startTime ASC')
            ->setParameter('is', $intervalStart)
            ->setParameter('ie', $intervalEnd)
            ->setParameter('uid', $uid)
            ->setParameter('spid', $spid)
            ->getResult();
    }

    public function findPostedShifts()
    {
        return $this->getEntityManager()
            ->createQuery('SELECT shift FROM OpenSkedgeBundle:Shift shift
                WHERE (shift.endTime > CURRENT_TIMESTAMP() AND shift.status != \'unapproved\')')
            ->getResult();
    }

    public function findUserPastShifts($uid)
    {
        return $this->getEntityManager()->createQuery('SELECT shift FROM OpenSkedgeBundle:Shift shift
                WHERE (shift.endTime < CURRENT_TIMESTAMP() AND shift.pickedUpBy = :uid AND shift.status != \'unapproved\') ORDER BY shift.endTime DESC')
            ->setParameter('uid', $uid)
            ->getResult();
    }

    public function supportsClass($class)
    {
        return $this->getEntityName() === $class || is_subclass_of($class, $this->getEntityName());
    }
}
