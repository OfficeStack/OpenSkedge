<?php

namespace OpenSkedge\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * OpenSkedge\AppBundle\Entity\Shift
 *
 * @ORM\Table(name="os_shift", indexes={@ORM\Index(name="users", columns={"uid", "puid"})})
 * @ORM\Entity(repositoryClass="OpenSkedge\AppBundle\Entity\ShiftRepository")
 */
class Shift extends BaseEntity\ShiftBaseEntity
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\Choice(choices = {"pending", "approved", "unapproved"}, message = "Choose a valid status.")
     * @Assert\Type(type="string")
     */
    protected $status;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $creationTime;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $startTime;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $endTime;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="shifts")
     * @ORM\JoinColumn(name="uid", referencedColumnName="id")
     **/
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="pickedUpShifts")
     * @ORM\JoinColumn(name="puid", referencedColumnName="id")
     **/
    protected $pickedUpBy;

    /**
     * @ORM\ManyToOne(targetEntity="Schedule", inversedBy="shifts")
     * @ORM\JoinColumn(name="sid", referencedColumnName="id")
     */
    protected $schedule;

    /**
     * @ORM\ManyToOne(targetEntity="SchedulePeriod", inversedBy="shifts")
     * @ORM\JoinColumn(name="spid", referencedColumnName="id")
     */
    protected $schedulePeriod;

    /**
     * @ORM\ManyToOne(targetEntity="Position", inversedBy="shifts")
     * @ORM\JoinColumn(name="pid", referencedColumnName="id")
     */
    protected $position;

    public function __construct()
    {
        parent::__construct();
        $this->status = "pending";
        $this->creationTime = new \DateTime("now");
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
     * Set creationTime
     *
     * @param \DateTime $creationTime
     * @return Shift
     */
    public function setCreationTime($creationTime)
    {
        $this->creationTime = $creationTime;

        return $this;
    }

    /**
     * Get creationTime
     *
     * @return \DateTime
     */
    public function getCreationTime()
    {
        return $this->creationTime;
    }

    /**
     * Set startTime
     *
     * @param \DateTime $startTime
     * @return Shift
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
     * @return Shift
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
     * Set pickedUpBy
     *
     * @param \OpenSkedge\AppBundle\Entity\User $pickedUpBy
     * @return Shift
     */
    public function setPickedUpBy(\OpenSkedge\AppBundle\Entity\User $pickedUpBy = null)
    {
        $this->pickedUpBy = $pickedUpBy;

        return $this;
    }

    /**
     * Get pickedUpBy
     *
     * @return \OpenSkedge\AppBundle\Entity\User
     */
    public function getPickedUpBy()
    {
        return $this->pickedUpBy;
    }
}
