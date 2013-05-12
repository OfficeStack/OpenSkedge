<?php
// src/OpenSkedge/AppBundle/Entity/SchedulePeriod.php
namespace OpenSkedge\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * OpenSkedge\AppBundle\Entity\SchedulePeriod
 *
 * @ORM\Table(name="os_schedule_period")
 * @ORM\Entity()
 * @UniqueEntity(fields={"startTime", "endTime"})
 */
class SchedulePeriod
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="AvailabilitySchedule", mappedBy="schedulePeriod", cascade={"remove"})
     */
    private $availabilitySchedules;

    /**
     * @ORM\OneToMany(targetEntity="Schedule", mappedBy="schedulePeriod", cascade={"remove"})
     */
    private $schedules;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\DateTime(message="Invalid Start Date")
     */
    private $startTime;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\DateTime(message="Invalid End Date")
     */
    private $endTime;

    /**
     * @ORM\OneToMany(targetEntity="LateShift", mappedBy="schedulePeriod", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $lateShifts;

    /**
     * @ORM\OneToMany(targetEntity="Shift", mappedBy="schedulePeriod", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $shifts;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->availabilitySchedules = new \Doctrine\Common\Collections\ArrayCollection();
        $this->schedules = new \Doctrine\Common\Collections\ArrayCollection();
        $this->lateShifts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->shifts = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString()
    {
        return $this->getStartTime()->format('M-d-Y')." - ".$this->getEndTime()->format('M-d-Y');
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
     * Set startTime
     *
     * @param \DateTime $startTime
     * @return SchedulePeriod
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Get startTime
     *
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set endTime
     *
     * @param \DateTime $endTime
     * @return SchedulePeriod
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Get endTime
     *
     * @return \DateTime
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Add schedules
     *
     * @param \OpenSkedge\AppBundle\Entity\Schedule $schedules
     * @return SchedulePeriod
     */
    public function addSchedule(\OpenSkedge\AppBundle\Entity\Schedule $schedules)
    {
        $this->schedules[] = $schedules;

        return $this;
    }

    /**
     * Remove schedules
     *
     * @param \OpenSkedge\AppBundle\Entity\Schedule $schedules
     */
    public function removeSchedule(\OpenSkedge\AppBundle\Entity\Schedule $schedules)
    {
        $this->schedules->removeElement($schedules);
    }

    /**
     * Get schedules
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSchedules()
    {
        return $this->schedules;
    }

    /**
     * Add availabilitySchedules
     *
     * @param \OpenSkedge\AppBundle\Entity\AvailabilitySchedule $availabilitySchedules
     * @return SchedulePeriod
     */
    public function addAvailabilitySchedule(\OpenSkedge\AppBundle\Entity\AvailabilitySchedule $availabilitySchedules)
    {
        $this->availabilitySchedules[] = $availabilitySchedules;

        return $this;
    }

    /**
     * Remove availabilitySchedules
     *
     * @param \OpenSkedge\AppBundle\Entity\AvailabilitySchedule $availabilitySchedules
     */
    public function removeAvailabilitySchedule(\OpenSkedge\AppBundle\Entity\AvailabilitySchedule $availabilitySchedules)
    {
        $this->availabilitySchedules->removeElement($availabilitySchedules);
    }

    /**
     * Get availabilitySchedules
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAvailabilitySchedules()
    {
        return $this->availabilitySchedules;
    }

    /**
     * Add lateShifts
     *
     * @param \OpenSkedge\AppBundle\Entity\LateShift $lateShifts
     * @return Schedule
     */
    public function addLateShift(\OpenSkedge\AppBundle\Entity\LateShift $lateShifts)
    {
        $this->lateShifts[] = $lateShifts;

        return $this;
    }

    /**
     * Remove lateShifts
     *
     * @param \OpenSkedge\AppBundle\Entity\LateShift $lateShifts
     */
    public function removeLateShift(\OpenSkedge\AppBundle\Entity\LateShift $lateShifts)
    {
        $this->lateShifts->removeElement($lateShifts);
    }

    /**
     * Get lateShifts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLateShifts()
    {
        return $this->lateShifts;
    }

    /**
     * Add shifts
     *
     * @param \OpenSkedge\AppBundle\Entity\Shift $shifts
     * @return Schedule
     */
    public function addShift(\OpenSkedge\AppBundle\Entity\Shift $shifts)
    {
        $this->shifts[] = $shifts;

        return $this;
    }

    /**
     * Remove shifts
     *
     * @param \OpenSkedge\AppBundle\Entity\Shift $shifts
     */
    public function removeShift(\OpenSkedge\AppBundle\Entity\Shift $shifts)
    {
        $this->shifts->removeElement($shifts);
    }

    /**
     * Get shifts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getShifts()
    {
        return $this->shifts;
    }
}
