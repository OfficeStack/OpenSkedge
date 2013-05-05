<?php

namespace OpenSkedge\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * OpenSkedge\AppBundle\Entity\LateShift
 *
 * @ORM\Table(name="os_audit_late", indexes={@ORM\Index(name="lateShiftCreated", columns={"creationTime"})})
 * @ORM\Entity()
 */
class LateShift extends BaseEntity\ShiftBaseEntity
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
     * @Assert\Choice(choices = {"Unknown", "Excused", "Unexcused"}, message = "Choose a valid status.")
     * @Assert\Type(type="string")
     */
    protected $status;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $arrivalTime;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $creationTime;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     * @Assert\Type(type="string")
     */
    protected $notes;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="lateShifts")
     * @ORM\JoinColumn(name="uid", referencedColumnName="id")
     **/
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Schedule", inversedBy="lateShifts")
     * @ORM\JoinColumn(name="sid", referencedColumnName="id")
     */
    protected $schedule;

    public function __construct()
    {
        $this->status = "Unknown";
        $this->arrivalTime = null;
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
     * Set arrivalTime
     *
     * @param \DateTime $arrivalTime
     * @return LateShift
     */
    public function setArrivalTime($arrivalTime)
    {
        $this->arrivalTime = $arrivalTime;

        return $this;
    }

    /**
     * Get arrivalTime
     *
     * @return \DateTime
     */
    public function getArrivalTime()
    {
        return $this->arrivalTime;
    }

    /**
     * Set creationTime
     *
     * @param \DateTime $creationTime
     * @return LateShift
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
}
