<?php

namespace StudySauce\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="coupon")
 * @ORM\HasLifecycleCallbacks()
 */
class Coupon
{
    /**
     * @ORM\Column(type="integer", name="id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=256, name="name")
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=4096, name="description")
     */
    protected $description;

    /**
     * @ORM\ManyToOne(targetEntity="Pack", inversedBy="coupons")
     * @ORM\JoinColumn(name="pack_id", referencedColumnName="id", nullable=true)
     */
    protected $pack;

    /**
     * @ORM\Column(type="array", name="options", nullable=true)
     */
    protected $options;

    /**
     * @ORM\Column(type="datetime", name="valid_from", nullable=true)
     */
    protected $validFrom;

    /**
     * @ORM\Column(type="datetime", name="valid_to", nullable=true)
     */
    protected $validTo;

    /**
     * @ORM\Column(type="integer", name="max_uses")
     */
    protected $maxUses = 1;

    /**
     * @ORM\Column(type="string", length=32, name="seed")
     */
    protected $seed;

    /**
     * @ORM\Column(type="datetime", name="created")
     */
    protected $created;

    /**
     * @ORM\Column(type="boolean", name="deleted")
     */
    protected $deleted = false;

    /**
     * @ORM\OneToMany(targetEntity="Payment", mappedBy="coupon")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $payments;

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
     * Set name
     *
     * @param string $name
     * @return Coupon
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
     * @return Coupon
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
     * Set validFrom
     *
     * @param \DateTime $validFrom
     * @return Coupon
     */
    public function setValidFrom($validFrom)
    {
        $this->validFrom = $validFrom;

        return $this;
    }

    /**
     * Get validFrom
     *
     * @return \DateTime 
     */
    public function getValidFrom()
    {
        return $this->validFrom;
    }

    /**
     * Set validTo
     *
     * @param \DateTime $validTo
     * @return Coupon
     */
    public function setValidTo($validTo)
    {
        $this->validTo = $validTo;

        return $this;
    }

    /**
     * Get validTo
     *
     * @return \DateTime 
     */
    public function getValidTo()
    {
        return $this->validTo;
    }

    /**
     * Set maxUses
     *
     * @param integer $maxUses
     * @return Coupon
     */
    public function setMaxUses($maxUses)
    {
        $this->maxUses = $maxUses;

        return $this;
    }

    /**
     * Get maxUses
     *
     * @return integer 
     */
    public function getMaxUses()
    {
        return $this->maxUses;
    }

    /**
     * Set seed
     *
     * @param string $seed
     * @return Coupon
     */
    public function setSeed($seed)
    {
        $this->seed = $seed;

        return $this;
    }

    /**
     * Get seed
     *
     * @return string 
     */
    public function getSeed()
    {
        return $this->seed;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Coupon
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
     * Set deleted
     *
     * @param boolean $deleted
     * @return Coupon
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean 
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set group
     *
     * @param \StudySauce\Bundle\Entity\Group $group
     * @return Coupon
     */
    public function setGroup(\StudySauce\Bundle\Entity\Group $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return \StudySauce\Bundle\Entity\Group 
     */
    public function getGroup()
    {
        return $this->group;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->payments = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set options
     *
     * @param array $options
     * @return Coupon
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Get options
     *
     * @return array 
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Add payments
     *
     * @param \StudySauce\Bundle\Entity\Payment $payments
     * @return Coupon
     */
    public function addPayment(\StudySauce\Bundle\Entity\Payment $payments)
    {
        $this->payments[] = $payments;

        return $this;
    }

    /**
     * Remove payments
     *
     * @param \StudySauce\Bundle\Entity\Payment $payments
     */
    public function removePayment(\StudySauce\Bundle\Entity\Payment $payments)
    {
        $this->payments->removeElement($payments);
    }

    /**
     * Get payments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPayments()
    {
        return $this->payments;
    }
}
