<?php

namespace OpenSkedge\AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ArchivedClockRepository extends EntityRepository
{
    public function getDistinctWeeks()
    {
        return $this->createQueryBuilder('ac')
            ->select('DISTINCT ac.week')
            ->orderBy('ac.week', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function supportsClass($class)
    {
        return $this->getEntityName() === $class || is_subclass_of($class, $this->getEntityName());
    }
}
