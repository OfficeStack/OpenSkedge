<?php
// src/OpenSkedge/AppBundle/Entity/Area.php
namespace OpenSkedge\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\NotBlank(message="An Area's name cannot be left blank.")
     * @Assert\Length(min = "2", max = "50",
     *      minMessage = "An Area's name must be at least {{ limit }} characters length",
     *      maxMessage = "An Area's name cannot be longer than than {{ limit }} characters length"
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=300, nullable=true)
     * @Assert\Length(max = "50",
     *      maxMessage = "An Area's description cannot be longer than than {{ limit }} characters length"
     * )
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
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->positions = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add positions
     *
     * @param \OpenSkedge\AppBundle\Entity\Position $positions
     * @return Area
     */
    public function addPosition(\OpenSkedge\AppBundle\Entity\Position $positions)
    {
        $this->positions[] = $positions;

        return $this;
    }

    /**
     * Remove positions
     *
     * @param \OpenSkedge\AppBundle\Entity\Position $positions
     */
    public function removePosition(\OpenSkedge\AppBundle\Entity\Position $positions)
    {
        $this->positions->removeElement($positions);
    }

    /**
     * Get positions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPositions()
    {
        return $this->positions;
    }
}
