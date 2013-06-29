<?php

namespace OpenSkedge\AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ScheduleRepository extends EntityRepository
{
    /**
     * Find user schedules by schedule period
     *
     * @param integer $uid  User's ID
     * @param integer $spid Schedule period ID
     *
     * @return DoctrineCollection
     */
    public function findUserSchedulesBySchedulePeriod($uid, $spid)
    {
        return $this->getEntityManager()->createQuery('SELECT s FROM OpenSkedgeBundle:Schedule s
                    WHERE (s.schedulePeriod = :spid AND s.user = :uid)')
            ->setParameter('uid', $uid)
            ->setParameter('spid', $spid)
            ->getResult();
    }

    public function supportsClass($class)
    {
        return $this->getEntityName() === $class || is_subclass_of($class, $this->getEntityName());
    }
}
