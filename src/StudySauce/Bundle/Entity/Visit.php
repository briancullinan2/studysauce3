<?php

namespace StudySauce\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="visit",indexes={
 *     @ORM\Index(name="session_idx", columns={"session_id", "user_id"}),
 *     @ORM\Index(name="path_idx", columns={"path", "session_id", "user_id"})})
 *     @ORM\Index(name="created_idx", columns={"path", "user_id", "created"})})
 * @ORM\HasLifecycleCallbacks()
 */
class Visit
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Session", inversedBy="visits")
     * @ORM\Column(type="string", name="session_id", length=64, nullable=true)
     */
    protected $session;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="visits")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    protected $user;

    /**
     * @ORM\Column(type="string", length=256, name="path")
     */
    protected $path;

    /**
     * @ORM\Column(type="array", name="query", nullable=true)
     */
    protected $query;

    /**
     * @ORM\Column(type="string", length=256, name="hash")
     */
    protected $hash;

    /**
     * @ORM\Column(type="string", length=8, name="method")
     */
    protected $method;

    /**
     * @ORM\Column(type="bigint", length=12, name="ip")
     */
    protected $ip;

    /**
     * @ORM\Column(type="datetime", name="created")
     */
    protected $created;

    /**
     * @ORM\PrePersist
     */
    public function setCreatedValue()
    {
        $this->created = new \DateTime();
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
     * Set path
     *
     * @param string $path
     * @return Visit
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set query
     *
     * @param array $query
     * @return Visit
     */
    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Get query
     *
     * @return array
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set hash
     *
     * @param string $hash
     * @return Visit
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string 
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set method
     *
     * @param string $method
     * @return Visit
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Get method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Visit
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set session
     *
     * @param string $session
     * @return Visit
     */
    public function setSession($session = null)
    {
        $this->session = $session;

        return $this;
    }

    /**
     * Get session
     *
     * @return string
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Set user
     *
     * @param \StudySauce\Bundle\Entity\User $user
     * @return Visit
     */
    public function setUser(\StudySauce\Bundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \StudySauce\Bundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set ip
     *
     * @param integer $ip
     * @return Visit
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return integer 
     */
    public function getIp()
    {
        return $this->ip;
    }
}
