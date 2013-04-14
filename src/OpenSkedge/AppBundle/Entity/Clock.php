<?php
// src/OpenSkedge/AppBundle/Entity/Clock.php
namespace OpenSkedge\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="os_clock")
 * @ORM\Entity()
 */
class Clock
{
    /**
     * @ORM\Column(name="id", type="integer", unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=96)
     */
    private $sun;

    /**
     * @ORM\Column(type="string", length=96)
     */
    private $mon;

    /**
     * @ORM\Column(type="string", length=96)
     */
    private $tue;

    /**
     * @ORM\Column(type="string", length=96)
     */
    private $wed;

    /**
     * @ORM\Column(type="string", length=96)
     */
    private $thu;

    /**
     * @ORM\Column(type="string", length=96)
     */
    private $fri;

    /**
     * @ORM\Column(type="string", length=96)
     */
    private $sat;

    /**
     * @ORM\Column(name="last_clock", type="datetime")
     */
    private $lastClock;

    public function __construct()
    {
        $this->status = 0;
        $this->sun = "000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000";
        $this->mon = "000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000";
        $this->tue = "000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000";
        $this->wed = "000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000";
        $this->thu = "000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000";
        $this->fri = "000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000";
        $this->sat = "000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000";
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
     * Set sun
     *
     * @param string $sun
     * @return Clock
     */
    public function setSun($sun)
    {
        $this->sun = $sun;

        return $this;
    }

    /**
     * Get sun
     *
     * @return string
     */
    public function getSun()
    {
        return $this->sun;
    }

    /**
     * Set mon
     *
     * @param string $mon
     * @return Clock
     */
    public function setMon($mon)
    {
        $this->mon = $mon;

        return $this;
    }

    /**
     * Get mon
     *
     * @return string
     */
    public function getMon()
    {
        return $this->mon;
    }

    /**
     * Set tue
     *
     * @param string $tue
     * @return Clock
     */
    public function setTue($tue)
    {
        $this->tue = $tue;

        return $this;
    }

    /**
     * Get tue
     *
     * @return string
     */
    public function getTue()
    {
        return $this->tue;
    }

    /**
     * Set wed
     *
     * @param string $wed
     * @return Clock
     */
    public function setWed($wed)
    {
        $this->wed = $wed;

        return $this;
    }

    /**
     * Get wed
     *
     * @return string
     */
    public function getWed()
    {
        return $this->wed;
    }

    /**
     * Set thu
     *
     * @param string $thu
     * @return Clock
     */
    public function setThu($thu)
    {
        $this->thu = $thu;

        return $this;
    }

    /**
     * Get thu
     *
     * @return string
     */
    public function getThu()
    {
        return $this->thu;
    }

    /**
     * Set fri
     *
     * @param string $fri
     * @return Clock
     */
    public function setFri($fri)
    {
        $this->fri = $fri;

        return $this;
    }

    /**
     * Get fri
     *
     * @return string
     */
    public function getFri()
    {
        return $this->fri;
    }

    /**
     * Set sat
     *
     * @param string $sat
     * @return Clock
     */
    public function setSat($sat)
    {
        $this->sat = $sat;

        return $this;
    }

    /**
     * Get sat
     *
     * @return string
     */
    public function getSat()
    {
        return $this->sat;
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
        for($i = 0; $i < 7; $i++) {
            $this->setDay($i, "000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000");
        }
        $this->setLastClock(new \DateTime("now"));

        return $this;
    }

    /**
     * Gets day value based on day number.
     *
     * @param int $dayint
     * @return string
     */
    public function getDay($dayint)
    {
        switch((int)$dayint) {
            case 0:
                $day = 'Sun';
                break;
            case 1:
                $day = 'Mon';
                break;
            case 2:
                $day = 'Tue';
                break;
            case 3:
                $day = 'Wed';
                break;
            case 4:
                $day = 'Thu';
                break;
            case 5:
                $day = 'Fri';
                break;
            case 6:
                $day = 'Sat';
                break;
            default:
                throw new \UnexpectedValueException('Input does not refer to a day!');
        }
        $getDay = 'get'.$day;
        return $this->$getDay();
    }

    /**
     * Sets day value based on day number.
     *
     * @param int $dayint
     * @param string $val
     * @return Clock
     */
    public function setDay($dayint, $val)
    {
        switch((int)$dayint) {
            case 0:
                $day = 'Sun';
                break;
            case 1:
                $day = 'Mon';
                break;
            case 2:
                $day = 'Tue';
                break;
            case 3:
                $day = 'Wed';
                break;
            case 4:
                $day = 'Thu';
                break;
            case 5:
                $day = 'Fri';
                break;
            case 6:
                $day = 'Sat';
                break;
            default:
                throw new \UnexpectedValueException('Input does not refer to a day!');
        }
        $setDay = 'set'.$day;
        return $this->$setDay($val);
    }

    public function setDayOffset($dayint, $offset, $val)
    {
        $recStr = $this->getDay($dayint);
        $recArr = str_split($recStr);
        $recArr[$offset] = $val;
        $rec = implode('', $recArr);
        return $this->setDay($dayint, $rec);
    }

    public function getDayOffset($dayint, $offset)
    {
        $recStr = $this->getDay($dayint);
        $recArr = str_split($recStr);
        return $recArr[$offset];
    }
}
