<?php
// src/OpenSkedge/AppBundle/Entity/Area.php
namespace OpenSkedge\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OpenSkedge\AppBundle\Entity\Area
 *
 * @ORM\Table(name="os_area")
 * @ORM\Entity()
 */
class Area
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=300, nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="Position", mappedBy="area", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $positions;

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
}
