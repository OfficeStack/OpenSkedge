<?php

namespace OpenSkedge\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * OpenSkedge\AppBundle\Entity\AvailabilitySchedule
 *
 * @ORM\Table(name="os_availability")
 * @ORM\Entity()
 * @UniqueEntity(fields={"user", "schedulePeriod"})
 */
class AvailabilitySchedule extends BaseEntity\RecordBaseEntity
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="availabilitySchedules")
     * @ORM\JoinColumn(name="uid", referencedColumnName="id")
     **/
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="SchedulePeriod", inversedBy="availabilitySchedules")
     * @ORM\JoinColumn(name="spid", referencedColumnName="id")
     */
    private $schedulePeriod;

    /**
     * @ORM\Column(type="string", length=140, nullable=true)
     * @Assert\Length(max="140")
     */
    private $notes;

    /**
     * @ORM\Column(name="last_updated", type="integer")
     */
    private $lastUpdated;

    public function __construct()
    {
        parent::__construct();
        $this->lastUpdated = time();
    }

    public function getWeek()
    {
        return array(str_split($this->getSun()), str_split($this->getMon()), str_split($this->getTue()), str_split($this->getWed()), str_split($this->getThu()), str_split($this->getFri()), str_split($this->getSat()));
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set notes
     *
     * @param string $notes
     * @return Schedule
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set lastupdated
     *
     * @return Schedule
     */
    public function setLastUpdated()
    {
        $this->lastUpdated = time();

        return $this;
    }

    /**
     * Get lastupdated
     *
     * @return integer
     */
    public function getLastUpdated()
    {
        return $this->lastUpdated;
    }

    /**
     * Set user
     *
     * @param \OpenSkedge\AppBundle\Entity\User $user
     * @return Schedule
     */
    public function setUser(\OpenSkedge\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \OpenSkedge\AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set schedulePeriod
     *
     * @param \OpenSkedge\AppBundle\Entity\SchedulePeriod $schedulePeriod
     * @return Schedule
     */
    public function setSchedulePeriod(\OpenSkedge\AppBundle\Entity\SchedulePeriod $schedulePeriod = null)
    {
        $this->schedulePeriod = $schedulePeriod;

        return $this;
    }

    /**
     * Get schedulePeriod
     *
     * @return \OpenSkedge\AppBundle\Entity\SchedulePeriod
     */
    public function getSchedulePeriod()
    {
        return $this->schedulePeriod;
    }
}
