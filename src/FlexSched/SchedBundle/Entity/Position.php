<?php
// src/FlexSched/SchedBundle/Entity/Position.php
namespace FlexSched\SchedBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FlexSched\SchedBundle\Entity\Position
 *
 * @ORM\Table(name="fs_position")
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
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=300)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="Area", inversedBy="positions")
     * @ORM\JoinColumn(name="area_id", referencedColumnName="id")
     */
    private $area;

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
     * @param \FlexSched\SchedBundle\Entity\Area $area
     * @return Position
     */
    public function setArea(\FlexSched\SchedBundle\Entity\Area $area = null)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get area
     *
     * @return \FlexSched\SchedBundle\Entity\Area
     */
    public function getArea()
    {
        return $this->area;
    }
}
