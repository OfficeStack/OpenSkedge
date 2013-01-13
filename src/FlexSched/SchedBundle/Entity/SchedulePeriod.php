<?php
// src/FlexSched/SchedBundle/Entity/SchedulePeriod.php
namespace FlexSched\SchedBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FlexSched\SchedBundle\Entity\SchedulePeriod
 *
 * @ORM\Table(name="os_schedule_period")
 * @ORM\Entity()
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
     * @ORM\OneToMany(targetEntity="Schedule", mappedBy="schedulePeriod", cascade={"remove"})
     */
    private $schedules;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startTime;

    /**
     * @ORM\Column(type="datetime")
     */
    private $endTime;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->schedules = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @param \FlexSched\SchedBundle\Entity\Schedule $schedules
     * @return SchedulePeriod
     */
    public function addSchedule(\FlexSched\SchedBundle\Entity\Schedule $schedules)
    {
        $this->schedules[] = $schedules;

        return $this;
    }

    /**
     * Remove schedules
     *
     * @param \FlexSched\SchedBundle\Entity\Schedule $schedules
     */
    public function removeSchedule(\FlexSched\SchedBundle\Entity\Schedule $schedules)
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
}
