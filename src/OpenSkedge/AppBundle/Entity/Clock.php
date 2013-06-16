<?php
// src/OpenSkedge/AppBundle/Entity/Clock.php
namespace OpenSkedge\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="os_clock")
 * @ORM\Entity()
 */
class Clock extends BaseEntity\RecordBaseEntity
{
    /**
     * @ORM\Column(name="id", type="integer", unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="User", inversedBy="clock")
     * @ORM\JoinColumn(name="uid", referencedColumnName="id")
     **/
    private $user;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @ORM\Column(name="last_clock", type="datetime")
     */
    private $lastClock;

    public function __construct()
    {
        parent::__construct();
        $this->status = 0;
        $this->lastClock = new \DateTime("now");
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
     * Set user
     *
     * @param \OpenSkedge\AppBundle\Entity\User $user
     * @return ArchivedClock
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
     * Set status
     *
     * @param boolean $status
     * @return Clock
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set lastClock
     *
     * @param \DateTime $lastClock
     * @return Clock
     */
    public function setLastClock($lastClock)
    {
        $this->lastClock = $lastClock;

        return $this;
    }

    /**
     * Get lastClock
     *
     * @return \DateTime
     */
    public function getLastClock()
    {
        return $this->lastClock;
    }

    /**
     * Reset time clock
     *
     * @return Clock
     */
    public function resetClock()
    {
        for ($i = 0; $i < 7; $i++) {
            $this->setDay($i, "000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000");
        }
        $this->setLastClock(new \DateTime("now"));

        return $this;
    }
}
