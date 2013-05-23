<?php
// src/OpenSkedge/AppBundle/Entity/Group.php
namespace OpenSkedge\AppBundle\Entity;

use Symfony\Component\Security\Core\Role\RoleInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Table(name="os_groups")
 * @ORM\Entity()
 * @Serializer\XmlRoot("group")
 */
class Group implements RoleInterface
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Type("integer")
     * @Serializer\ReadOnly
     */
    protected $id;

    /**
     * @ORM\Column(name="name", type="string", length=30)
     * @Serializer\Type("string")
     */
    protected $name;

    /**
     * @ORM\Column(name="role", type="string", length=20, unique=true)
     * @Serializer\Type("string")
     */
    protected $role;

    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="group")
     * @ORM\OrderBy({"name" = "ASC"})
     * @Serializer\Exclude
     */
    protected $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @see RoleInterface
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set role
     *
     * @param string $role
     * @return Group
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Add users
     *
     * @param \OpenSkedge\AppBundle\Entity\User $users
     * @return Group
     */
    public function addUser(\OpenSkedge\AppBundle\Entity\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \OpenSkedge\AppBundle\Entity\User $users
     */
    public function removeUser(\OpenSkedge\AppBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }
}
