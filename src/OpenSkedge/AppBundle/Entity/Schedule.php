<?php
// src/OpenSkedge/AppBundle/Entity/Schedule.php
namespace OpenSkedge\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * OpenSkedge\AppBundle\Entity\Schedule
 *
 * @ORM\Table(name="os_schedule")
 * @ORM\Entity()
 * @UniqueEntity(fields={"user", "schedulePeriod", "position"})
 */
class Schedule extends BaseEntity\RecordBaseEntity
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="schedules")
     * @ORM\JoinColumn(name="uid", referencedColumnName="id")
     **/
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="SchedulePeriod", inversedBy="schedules")
     * @ORM\JoinColumn(name="spid", referencedColumnName="id")
     */
    private $schedulePeriod;

    /**
     * @ORM\ManyToOne(targetEntity="Position", inversedBy="schedules")
     * @ORM\JoinColumn(name="pid", referencedColumnName="id")
     */
    private $position;

    /**
     * @ORM\Column(type="string", length=140, nullable=true)
     * @Assert\Length(max="140")
     */
    private $notes;

    /**
     * @ORM\Column(name="last_updated", type="integer")
     */
    private $lastUpdated;

    /**
     * @ORM\OneToMany(targetEntity="LateShift", mappedBy="schedule", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $lateShifts;

    /**
     * @ORM\OneToMany(targetEntity="Shift", mappedBy="schedule", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $shifts;

    public function __construct()
    {
        parent::__construct();
        $this->lastUpdated = time();
        $this->lateShifts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->shifts = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @param integer $lastupdated
     * @return Schedule
     */
    public function setLastUpdated($time = null)
    {
        if ($time === null) {
            $this->lastUpdated = time();
        } else {
            $this->lastUpdated = $time;
        }

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

    /**
     * Set position
     *
     * @param \OpenSkedge\AppBundle\Entity\Position $position
     * @return Schedule
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
