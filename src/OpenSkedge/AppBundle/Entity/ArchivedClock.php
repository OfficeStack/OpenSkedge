<?php
// src/OpenSkedge/AppBundle/Entity/ArchivedClock.php
namespace OpenSkedge\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="os_archived_clock", indexes={@ORM\Index(name="week", columns={"week"})})
 * @ORM\Entity()
 */
class ArchivedClock
{
    /**
     * @ORM\Column(name="id", type="integer", unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="archivedClocks")
     * @ORM\JoinColumn(name="uid", referencedColumnName="id")
     **/
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $week;

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

    public function __construct()
    {
        $this->sun = "000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000";
        $this->mon = "000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000";
        $this->tue = "000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000";
        $this->wed = "000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000";
        $this->thu = "000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000";
        $this->fri = "000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000";
        $this->sat = "000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000";
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
        }
        $getDay = 'get'.$day;
        return $this->$getDay();
    }

    /**
     * Sets day value based on day number.
     *
     * @param int $dayint
     * @param string $val
     * @return ArchivedClock
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
                return $this;
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

    /**
     * Set sun
     *
     * @param string $sun
     * @return ArchivedClock
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
     * @return ArchivedClock
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
     * @return ArchivedClock
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
     * @return ArchivedClock
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
     * @return ArchivedClock
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
     * @return ArchivedClock
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
     * @return ArchivedClock
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
     * Set week
     *
     * @param \DateTime $week
     * @return ArchivedClock
     */
    public function setWeek($week)
    {
        $this->week = $week;

        return $this;
    }

    /**
     * Get week
     *
     * @return \DateTime
     */
    public function getWeek()
    {
        return $this->week;
    }
}
