<?php

namespace OpenSkedge\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ApiToken
 *
 * @ORM\Table("os_user_api_tokens")
 * @ORM\Entity()
 */
class ApiToken
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=64)
     */
    private $token;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="apiTokens")
     * @ORM\JoinColumn(name="uid", referencedColumnName="id")
     **/
    private $user;

    public function __construct()
    {
        $this->token = hash('sha256', (string)uniqid('', true).time());
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
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return ApiToken
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}
