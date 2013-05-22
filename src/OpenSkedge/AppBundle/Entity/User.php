<?php
// src/OpenSkedge/AppBundle/Entity/User.php
namespace OpenSkedge\AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * OpenSkedge\AppBundle\Entity\User
 *
 * @ORM\Table(name="os_user")
 * @ORM\Entity(repositoryClass="OpenSkedge\AppBundle\Entity\UserRepository")
 */
class User implements AdvancedUserInterface, \Serializable
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30, unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(min="4", max="30",
     *                minMessage="Username must be {{ limit }} or more characters.",
     *                maxMessage="Username cannot be more than {{ limit }} characters."
     * )
     * @Assert\Type(type="string", message="Username must be text.")
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\NotBlank()
     */
    private $salt;

    /**
     * @ORM\Column(type="string", length=128)
     * @Assert\NotBlank(groups={"user_creation"})
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\NotBlank()
     * @Assert\Length(min="2", max="64",
     *                minMessage="Name must be {{ limit }} or more characters.",
     *                maxMessage="Name cannot be more than {{ limit }} characters."
     * )
     * @Assert\Type(type="string", message="Name must be text.")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $workphone;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $homephone;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    private $location;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="integer")
     */
    private $min;

    /**
     * @ORM\Column(type="integer")
     */
    private $max;

    /**
     * @ORM\Column(type="integer")
     */
    private $hours;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $notes;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $supnotes;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $color;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     * @Assert\NotNull()
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="users")
     * @ORM\JoinColumn(name="gid", referencedColumnName="id")
     */
    private $group;

    /**
     * @ORM\OneToOne(targetEntity="Clock", mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $clock;

    /**
     * @ORM\OneToMany(targetEntity="ArchivedClock", mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(name="uid", referencedColumnName="id")
     */
    private $archivedClocks;

    /**
     * @ORM\OneToMany(targetEntity="Schedule", mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $schedules;

    /**
     * @ORM\OneToMany(targetEntity="AvailabilitySchedule", mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $availabilitySchedules;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="employees")
     * @ORM\JoinTable(name="os_user_sup",
     *      joinColumns={@ORM\JoinColumn(name="emp_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="sup_id", referencedColumnName="id")}
     *      )
     * @ORM\OrderBy({"name" = "ASC"})
     **/
    private $supervisors;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="supervisors")
     * @ORM\OrderBy({"name" = "ASC"})
     **/
    private $employees;

    /**
     * @ORM\OneToMany(targetEntity="LateShift", mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $lateShifts;

    /**
     * @ORM\OneToMany(targetEntity="Shift", mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $shifts;

    /**
     * @ORM\OneToMany(targetEntity="Shift", mappedBy="pickedUpBy", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $pickedUpShifts;

    /**
     * @ORM\OneToMany(targetEntity="ApiToken", mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $apiTokens;

    public function __construct()
    {
        $this->min = 10;
        $this->max = 20;
        $this->hours = 15;
        $this->isActive = true;
        $this->color = "#".dechex(rand(0,15)).dechex(rand(0,15)).dechex(rand(0,15)).dechex(rand(0,15)).dechex(rand(0,15)).dechex(rand(0,15));
        $this->salt = hash('sha256', uniqid(null, true));
        $this->availabilitySchedules = new ArrayCollection();
        $this->schedules = new ArrayCollection();
        $this->supervisors = new ArrayCollection();
        $this->employees = new ArrayCollection();
        $this->archivedClocks = new ArrayCollection();
        $this->lateShifts = new ArrayCollection();
        $this->shifts = new ArrayCollection();
        $this->pickedUpShifts = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name."[".$this->username."]";
    }

    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @inheritDoc
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return User
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
     * @inheritDoc
     */
    public function getRoles()
    {
        return array($this->group);
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return (int)$this->id;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Set workphone
     *
     * @param string $workphone
     * @return User
     */
    public function setWorkphone($workphone)
    {
        $this->workphone = $workphone;

        return $this;
    }

    /**
     * Get workphone
     *
     * @return string
     */
    public function getWorkphone()
    {
        return $this->workphone;
    }

    /**
     * Set homephone
     *
     * @param string $homephone
     * @return User
     */
    public function setHomephone($homephone)
    {
        $this->homephone = $homephone;

        return $this;
    }

    /**
     * Get homephone
     *
     * @return string
     */
    public function getHomephone()
    {
        return $this->homephone;
    }

    /**
     * Set location
     *
     * @param string $location
     * @return User
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set min
     *
     * @param integer $min
     * @return User
     */
    public function setMin($min)
    {
        $this->min = $min;

        return $this;
    }

    /**
     * Get min
     *
     * @return integer
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * Set max
     *
     * @param integer $max
     * @return User
     */
    public function setMax($max)
    {
        $this->max = $max;

        return $this;
    }

    /**
     * Get max
     *
     * @return integer
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * Set hours
     *
     * @param integer $hours
     * @return User
     */
    public function setHours($hours)
    {
        $this->hours = $hours;

        return $this;
    }

    /**
     * Get hours
     *
     * @return integer
     */
    public function getHours()
    {
        return $this->hours;
    }

    /**
     * Set notes
     *
     * @param string $notes
     * @return User
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set supnotes
     *
     * @param string $supnotes
     * @return User
     */
    public function setSupnotes($supnotes)
    {
        $this->supnotes = $supnotes;

        return $this;
    }

    /**
     * Get supnotes
     *
     * @return string
     */
    public function getSupnotes()
    {
        return $this->supnotes;
    }

    /**
     * Set color
     *
     * @param string $color
     * @return User
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
        ) = unserialize($serialized);
    }

    public function equals(UserInterface $user)
    {
        if (!$user instanceof User) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->getSalt() !== $user->getSalt()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->isActive;
    }

    /**
     * Enable user
     *
     * @return User
     */
    public function enable()
    {
        $this->isActive = true;

        return $this;
    }

    /**
     * Disable user
     *
     * @return User
     */
    public function disable()
    {
        $this->isActive = false;

        return $this;
    }

    /**
     * Set clock
     *
     * @param \OpenSkedge\AppBundle\Entity\Clock $clock
     * @return User
     */
    public function setClock(\OpenSkedge\AppBundle\Entity\Clock $clock = null)
    {
        $this->clock = $clock;

        return $this;
    }

    /**
     * Get clock
     *
     * @return \OpenSkedge\AppBundle\Entity\Clock
     */
    public function getClock()
    {
        return $this->clock;
    }

    /**
     * Add schedules
     *
     * @param \OpenSkedge\AppBundle\Entity\Schedule $schedules
     * @return User
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

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        if ($this->isActive == 1)
            return true;
        else
            return false;
    }

    /**
     * Add supervisors
     *
     * @param \OpenSkedge\AppBundle\Entity\User $supervisors
     * @return User
     */
    public function addSupervisor(\OpenSkedge\AppBundle\Entity\User $supervisors)
    {
        $this->supervisors[] = $supervisors;

        return $this;
    }

    /**
     * Remove supervisors
     *
     * @param \OpenSkedge\AppBundle\Entity\User $supervisors
     */
    public function removeSupervisor(\OpenSkedge\AppBundle\Entity\User $supervisors)
    {
        $this->supervisors->removeElement($supervisors);
    }

    /**
     * Get supervisors
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSupervisors()
    {
        return $this->supervisors;
    }

    /**
     * Add employees
     *
     * @param \OpenSkedge\AppBundle\Entity\User $employees
     * @return User
     */
    public function addEmployee(\OpenSkedge\AppBundle\Entity\User $employees)
    {
        $this->employees[] = $employees;

        return $this;
    }

    /**
     * Remove employees
     *
     * @param \OpenSkedge\AppBundle\Entity\User $employees
     */
    public function removeEmployee(\OpenSkedge\AppBundle\Entity\User $employees)
    {
        $this->employees->removeElement($employees);
    }

    /**
     * Get employees
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEmployees()
    {
        return $this->employees;
    }

    /**
     * Set group
     *
     * @param \OpenSkedge\AppBundle\Entity\Group $group
     * @return User
     */
    public function setGroup(\OpenSkedge\AppBundle\Entity\Group $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return \OpenSkedge\AppBundle\Entity\Group
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Add availabilitySchedules
     *
     * @param \OpenSkedge\AppBundle\Entity\AvailabilitySchedule $availabilitySchedules
     * @return User
     */
    public function addAvailabilitySchedule(\OpenSkedge\AppBundle\Entity\AvailabilitySchedule $availabilitySchedules)
    {
        $this->availabilitySchedules[] = $availabilitySchedules;

        return $this;
    }

    /**
     * Remove availabilitySchedules
     *
     * @param \OpenSkedge\AppBundle\Entity\AvailabilitySchedule $availabilitySchedules
     */
    public function removeAvailabilitySchedule(\OpenSkedge\AppBundle\Entity\AvailabilitySchedule $availabilitySchedules)
    {
        $this->availabilitySchedules->removeElement($availabilitySchedules);
    }

    /**
     * Get availabilitySchedules
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAvailabilitySchedules()
    {
        return $this->availabilitySchedules;
    }

    /**
     * Add archivedClocks
     *
     * @param \OpenSkedge\AppBundle\Entity\ArchivedClock $schedules
     * @return User
     */
    public function addArchivedClock(\OpenSkedge\AppBundle\Entity\ArchivedClock $archivedClocks)
    {
        $this->archivedClocks[] = $archivedClocks;

        return $this;
    }

    /**
     * Remove archivedClocks
     *
     * @param \OpenSkedge\AppBundle\Entity\ArchivedClock $archivedClocks
     */
    public function removeArchivedClock(\OpenSkedge\AppBundle\Entity\ArchivedClock $archivedClocks)
    {
        $this->archivedClocks->removeElement($archivedClocks);
    }

    /**
     * Get schedules
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getArchivedClocks()
    {
        return $this->archivedClocks;
    }

    /**
     * Add lateShifts
     *
     * @param \OpenSkedge\AppBundle\Entity\LateShift $lateShifts
     * @return User
     */
    public function addLateShift(\OpenSkedge\AppBundle\Entity\LateShift $lateShifts)
    {
        $this->lateShifts[] = $lateShifts;

        return $this;
    }

    /**
     * Remove lateShifts
     *
     * @param \OpenSkedge\AppBundle\Entity\LateShift $lateShifts
     */
    public function removeLateShift(\OpenSkedge\AppBundle\Entity\LateShift $lateShifts)
    {
        $this->lateShifts->removeElement($lateShifts);
    }

    /**
     * Get lateShifts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLateShifts()
    {
        return $this->lateShifts;
    }

    /**
     * Add shifts
     *
     * @param \OpenSkedge\AppBundle\Entity\Shift $shifts
     * @return User
     */
    public function addShift(\OpenSkedge\AppBundle\Entity\Shift $shifts)
    {
        $this->shifts[] = $shifts;

        return $this;
    }

    /**
     * Remove shifts
     *
     * @param \OpenSkedge\AppBundle\Entity\Shift $shifts
     */
    public function removeShift(\OpenSkedge\AppBundle\Entity\Shift $shifts)
    {
        $this->shifts->removeElement($shifts);
    }

    /**
     * Get shifts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getShifts()
    {
        return $this->shifts;
    }

    /**
     * Add pickedUpShifts
     *
     * @param \OpenSkedge\AppBundle\Entity\Shift $pickedUpShifts
     * @return User
     */
    public function addPickedUpShift(\OpenSkedge\AppBundle\Entity\Shift $pickedUpShifts)
    {
        $this->pickedUpShifts[] = $pickedUpShifts;

        return $this;
    }

    /**
     * Remove pickedUpShifts
     *
     * @param \OpenSkedge\AppBundle\Entity\Shift $pickedUpShifts
     */
    public function removePickedUpShift(\OpenSkedge\AppBundle\Entity\Shift $pickedUpShifts)
    {
        $this->pickedUpShifts->removeElement($pickedUpShifts);
    }

    /**
     * Get pickedUpShifts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPickedUpShifts()
    {
        return $this->pickedUpShifts;
    }

    /**
     * Add ApiTokens
     *
     * @param \OpenSkedge\AppBundle\Entity\ApiToken $apiTokens
     * @return User
     */
    public function addApiToken(\OpenSkedge\AppBundle\Entity\ApiToken $apiTokens)
    {
        $this->apiTokens[] = $apiTokens;

        return $this;
    }

    /**
     * Remove ApiTokens
     *
     * @param \OpenSkedge\AppBundle\Entity\ApiToken $apiTokens
     */
    public function removeApiToken(\OpenSkedge\AppBundle\Entity\ApiToken $apiTokens)
    {
        $this->apiTokens->removeElement($apiTokens);
    }

    /**
     * Get ApiTokens
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getApiTokens()
    {
        return $this->apiTokens;
    }
}
