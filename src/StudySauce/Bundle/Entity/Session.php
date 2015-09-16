<?php

namespace StudySauce\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="session")
 */
class Session
{
    /**
     * @ORM\Column(type="string", length=128, name="session_id")
     * @ORM\Id
     */
    protected $id;

    /**
     * @ORM\Column(type="text", name="session_value")
     */
    protected $value;

    /**
     * @ORM\Column(type="integer", name="session_time")
     */
    protected $time;

    /**
     * @ORM\OneToMany(targetEntity="Visit", mappedBy="session")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $visits;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->visits = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set id
     *
     * @param string $id
     * @return Session
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
     * @return Session
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
     * Set time
     *
     * @param integer $time
     * @return Session
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return integer 
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Add visits
     *
     * @param \StudySauce\Bundle\Entity\Visit $visits
     * @return Session
     */
    public function addVisit(\StudySauce\Bundle\Entity\Visit $visits)
    {
        $this->visits[] = $visits;

        return $this;
    }

    /**
     * Remove visits
     *
     * @param \StudySauce\Bundle\Entity\Visit $visits
     */
    public function removeVisit(\StudySauce\Bundle\Entity\Visit $visits)
    {
        $this->visits->removeElement($visits);
    }

    /**
     * Get visits
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getVisits()
    {
        return $this->visits;
    }

    /**
     * Set lifetime
     *
     * @param integer $lifetime
     * @return Session
     */
    public function setLifetime($lifetime)
    {
        $this->lifetime = $lifetime;

        return $this;
    }

    /**
     * Get lifetime
     *
     * @return integer 
     */
    public function getLifetime()
    {
        return $this->lifetime;
    }
}
