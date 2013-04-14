<?php

namespace OpenSkedge\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * OpenSkedge\AppBundle\Entity\IP
 *
 * @ORM\Table(name="os_audit_ips")
 * @ORM\Entity()
 */

class IP
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotNull()
     * @Assert\Ip(version="all")
     */
    private $ip;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Assert\Length(min="2", max="64",
     *                minMessage="Name must be {{ limit }} or more characters.",
     *                maxMessage="Name cannot be more than {{ limit }} characters."
     * )
     * @Assert\Type(type="string", message="Name must be text.")
     */
    private $name;

    /**
     * @ORM\Column(name="allowed_to_clock", type="boolean")
     * @Assert\NotNull()
     */
    private $clockEnabled;

    public function __construct()
    {
        $this->clockEnabled = false;
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
     * Set IP Address
     *
     * @param string $ip IP address
     *
     * @return IP
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set name
     *
     * @param string $name A friendly identifier
     *
     * @return IP
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set clockEnabled
     *
     * @param boolean $clockEnabled Whether or not the IP is allowed to clock in.
     *
     * @return IP
     */
    public function setClockEnabled($clockEnabled)
    {
        $this->clockEnabled = $clockEnabled;

        return $this;
    }

    /**
     * Get clockEnabled
     *
     * @return boolean
     */
    public function isClockEnabled()
    {
        return $this->clockEnabled;
    }
}
