<?php

namespace OpenSkedge\AppBundle\Entity\BaseEntity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * OpenSkedge\AppBundle\Entity\BaseEntity\RecordBaseEntity
 *
 * @ORM\MappedSuperclass
 */
class RecordBaseEntity
{
    /**
     * @ORM\Column(type="string", length=96)
     * @Assert\Length(min="96", max="96")
     */
    protected $sun;

    /**
     * @ORM\Column(type="string", length=96)
     * @Assert\Length(min="96", max="96")
     */
    protected $mon;

    /**
     * @ORM\Column(type="string", length=96)
     * @Assert\Length(min="96", max="96")
     */
    protected $tue;

    /**
     * @ORM\Column(type="string", length=96)
     * @Assert\Length(min="96", max="96")
     */
    protected $wed;

    /**
     * @ORM\Column(type="string", length=96)
     * @Assert\Length(min="96", max="96")
     */
    protected $thu;

    /**
     * @ORM\Column(type="string", length=96)
     * @Assert\Length(min="96", max="96")
     */
    protected $fri;

    /**
     * @ORM\Column(type="string", length=96)
     * @Assert\Length(min="96", max="96")
     */
    protected $sat;

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
     * @return RecordBaseEntity
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

    /**
     * Set sun
     *
     * @param string $sun
     * @return RecordBaseEntity
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
     * @return RecordBaseEntity
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
     * @return RecordBaseEntity
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
     * @return RecordBaseEntity
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
     * @return RecordBaseEntity
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
     * @return RecordBaseEntity
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
     * @return RecordBaseEntity
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
}
