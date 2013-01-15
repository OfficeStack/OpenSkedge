<?php
// src/OpenSkedge/AppBundle/Entity/Position.php
namespace OpenSkedge\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * OpenSkedge\AppBundle\Entity\Position
 *
 * @ORM\Table(name="os_position")
 * @ORM\Entity()
 */
class Position
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank(message="A Position's name cannot be left blank.")
     * @Assert\Length(min = "2", max = "50",
     *      minMessage = "A Position's name must be at least {{ limit }} characters length",
     *      maxMessage = "A Position's name cannot be longer than than {{ limit }} characters length"
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=300)
     * @Assert\Length(max = "50",
     *      maxMessage = "An Area's description cannot be longer than than {{ limit }} characters length"
     * )
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="Area", inversedBy="positions")
     * @ORM\JoinColumn(name="area_id", referencedColumnName="id")
     */
    private $area;

    /**
     * @ORM\OneToMany(targetEntity="Schedule", mappedBy="position", cascade={"remove"})
     */
    private $schedules;

    public function __construct()
    {
        $this->schedules = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString()
    {
        return $this->id." ".$this->name;
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
     * Set name
     *
     * @param string $name
     * @return Position
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
     * Set description
     *
     * @param string $description
     * @return Position
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set area
     *
     * @param \OpenSkedge\AppBundle\Entity\Area $area
     * @return Position
     */
    public function setArea(\OpenSkedge\AppBundle\Entity\Area $area = null)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get area
     *
     * @return \OpenSkedge\AppBundle\Entity\Area
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * Add schedules
     *
     * @param \OpenSkedge\AppBundle\Entity\Schedule $schedules
     * @return Position
     */
    public function addSchedule(\OpenSkedge\AppBundle\Entity\Schedule $schedules)
    {
        $this->schedules[] = $schedules;

        return $this;
    }

    /**
     * Remove schedules
     *
     * @param \OpenSkedge\AppBundle\Entity\Schedule $schedules
     */
    public function removeSchedule(\OpenSkedge\AppBundle\Entity\Schedule $schedules)
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
