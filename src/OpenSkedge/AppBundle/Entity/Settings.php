<?php

namespace OpenSkedge\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * OpenSkedge\AppBundle\Entity\Settings
 *
 * @ORM\Table(name="os_settings")
 * @ORM\Entity()
 */

class Settings
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(min="2", max="64",
     *                minMessage="Brand Name must be {{ limit }} or more characters.",
     *                maxMessage="Brand Name cannot be more than {{ limit }} characters."
     * )
     */
    private $brandName;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Type(type="integer")
     */
    private $pruneAfter;

    /**
     * @ORM\Column(type="string", length=9)
     * @Assert\NotBlank()
     * @Assert\Choice(choices = {"sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday"}, message = "Choose a valid day of the week.")
     * @Assert\Type(type="string")
     */
    private $weekStartDay;

    /**
     * @ORM\Column(type="string", length=9)
     * @Assert\NotBlank()
     * @Assert\Choice(choices = {"sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday"}, message = "Choose a valid day of the week.")
     * @Assert\Type(type="string")
     */
    private $weekStartDayClock;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\Choice(choices = {"15 mins", "30 mins", "1 hour"}, message = "Choose a valid time resolution")
     * @Assert\Type(type="string")
     */
    private $defaultTimeResolution;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     */
    private $startHour;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     */
    private $endHour;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->id = 1; // OpenSkedge expects application settings to be ID 1.
        $this->allowedClockIps = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set brandName
     *
     * @param string $brandName
     * @return Settings
     */
    public function setBrandName($brandName)
    {
        $this->brandName = $brandName;

        return $this;
    }

    /**
     * Get brandName
     *
     * @return string
     */
    public function getBrandName()
    {
        return $this->brandName;
    }

    /**
     * Set pruneAfter
     *
     * @param integer $pruneAfter
     * @return Settings
     */
    public function setPruneAfter($pruneAfter)
    {
        $this->pruneAfter = $pruneAfter;

        return $this;
    }

    /**
     * Get pruneAfter
     *
     * @return integer
     */
    public function getPruneAfter()
    {
        return $this->pruneAfter;
    }

    /**
     * Set weekStartDay
     *
     * @param string $weekStartDay
     * @return Settings
     */
    public function setWeekStartDay($weekStartDay)
    {
        $this->weekStartDay = $weekStartDay;

        return $this;
    }

    /**
     * Get weekStartDay
     *
     * @return string
     */
    public function getWeekStartDay()
    {
        return $this->weekStartDay;
    }

    /**
     * Set weekStartDayClock
     *
     * @param string $weekStartDayClock
     * @return Settings
     */
    public function setWeekStartDayClock($weekStartDayClock)
    {
        $this->weekStartDayClock = $weekStartDayClock;

        return $this;
    }

    /**
     * Get weekStartDayClock
     *
     * @return string
     */
    public function getWeekStartDayClock()
    {
        return $this->weekStartDayClock;
    }

    /**
     * Set defaultTimeResolution
     *
     * @param string $defaultTimeResolution
     * @return Settings
     */
    public function setDefaultTimeResolution($defaultTimeResolution)
    {
        $this->defaultTimeResolution = $defaultTimeResolution;

        return $this;
    }

    /**
     * Get defaultTimeResolution
     *
     * @return string
     */
    public function getDefaultTimeResolution()
    {
        return $this->defaultTimeResolution;
    }

    /**
     * Set startHour
     *
     * @param string $startHour
     * @return Settings
     */
    public function setStartHour($startHour)
    {
        $this->startHour = date("H:i:s", strtotime($startHour));

        return $this;
    }

    /**
     * Get startHour
     *
     * @return string
     */
    public function getStartHour()
    {
        return date("g:i A", strtotime($this->startHour));
    }

    /**
     * Set endHour
     *
     * @param string $endHour
     * @return Settings
     */
    public function setEndHour($endHour)
    {
        $this->endHour = date("H:i:s", strtotime($endHour));

        return $this;
    }

    /**
     * Get endHour
     *
     * @return string
     */
    public function getEndHour()
    {
        return date("g:i A", strtotime($this->endHour));
    }
}
