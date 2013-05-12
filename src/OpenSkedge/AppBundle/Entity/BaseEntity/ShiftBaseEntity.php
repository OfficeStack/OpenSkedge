<?php

namespace OpenSkedge\AppBundle\Entity\BaseEntity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
* OpenSkedge\AppBundle\Entity\BaseEntity\ShiftBaseEntity
*
* @ORM\MappedSuperclass
*/
class ShiftBaseEntity
{
   /**
    * @ORM\Column(type="string")
    * @Assert\NotBlank()
    * @Assert\Type(type="string")
    */
   protected $status;

   /**
    * @ORM\Column(type="string", nullable=true, length=255)
    * @Assert\Type(type="string")
    */
   protected $notes;

   protected $user;

   protected $schedulePeriod;

   protected $position;

   protected $schedule;

   /**
     * Set notes
     *
     * @param string $notes
     * @return LateShift
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
     * Set user
     *
     * @param \OpenSkedge\AppBundle\Entity\User $user
     * @return LateShift
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
     * Set schedule
     *
     * @param \OpenSkedge\AppBundle\Entity\Schedule $schedule
     * @return LateShift
     */
    public function setSchedule(\OpenSkedge\AppBundle\Entity\Schedule $schedule = null)
    {
        $this->schedule = $schedule;

        return $this;
    }

    /**
     * Get schedule
     *
     * @return \OpenSkedge\AppBundle\Entity\Schedule
     */
    public function getSchedule()
    {
        return $this->schedule;
    }

    /**
     * Set schedulePeriod
     *
     * @param \OpenSkedge\AppBundle\Entity\SchedulePeriod $schedulePeriod
     * @return LateShift
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

    /**
     * Set position
     *
     * @param \OpenSkedge\AppBundle\Entity\Position $position
     * @return LateShift
     */
    public function setPosition(\OpenSkedge\AppBundle\Entity\Position $position = null)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return \OpenSkedge\AppBundle\Entity\Position
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return LateShift
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }
}
