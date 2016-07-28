<?php

namespace StudySauce\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="rememberme_token")
 */
class Token
{
    /**
     * @ORM\Column(type="string", length=88, name="series")
     * @ORM\Id
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=88, name="value")
     */
    protected $value;

    /**
     * @ORM\Column(type="datetime", name="lastUsed")
     */
    protected $lastUsed;

    /**
     * @ORM\Column(type="string", length=100, name="class")
     */
    protected $class;

    /**
     * @ORM\Column(type="string", length=200, name="username")
     */
    protected $username;


    /**
     * Set id
     *
     * @param string $id
     *
     * @return Token
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return Token
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set lastUsed
     *
     * @param \DateTime $lastUsed
     *
     * @return Token
     */
    public function setLastUsed($lastUsed)
    {
        $this->lastUsed = $lastUsed;

        return $this;
    }

    /**
     * Get lastUsed
     *
     * @return \DateTime
     */
    public function getLastUsed()
    {
        return $this->lastUsed;
    }

    /**
     * Set class
     *
     * @param string $class
     *
     * @return Token
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Get class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return Token
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }
}
