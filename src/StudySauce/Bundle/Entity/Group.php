<?php

namespace StudySauce\Bundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\Group as BaseGroup;
use FOS\UserBundle\Model\GroupInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="ss_group")
 * @ORM\HasLifecycleCallbacks()
 */
class Group extends BaseGroup implements GroupInterface
{
    /**
     * @ORM\Column(type="integer", name="id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=256, name="description")
     */
    protected $description;

    /**
     * @ORM\Column(type="datetime", name="created")
     */
    protected $created;

    /**
     * @ORM\OneToMany(targetEntity="Coupon", mappedBy="group", fetch="EXTRA_LAZY")
     */
    protected $coupons;

    /**
     * @ORM\OneToMany(targetEntity="Invite", mappedBy="group", fetch="EXTRA_LAZY")
     */
    protected $invites;

    /**
     * @ORM\OneToMany(targetEntity="Pack", mappedBy="group", fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $packs;

    /**
     * @ORM\ManyToMany(targetEntity="Pack", mappedBy="groups", fetch="EXTRA_LAZY")
     */
    protected $group_packs;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="groups", fetch="EXTRA_LAZY")
     */
    protected $users;

    /**
     * @ORM\ManyToOne(targetEntity="File")
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id", nullable = true)
     */
    protected $logo;

    /**
     * @ORM\Column(type="boolean", name="deleted")
     */
    protected $deleted = false;

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
     * Set description
     *
     * @param string $description
     * @return Group
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
     * Set created
     *
     * @param \DateTime $created
     * @return Group
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
     * Constructor
     * @param null $name
     * @param array $roles
     */
    public function __construct($name = null, $roles = array()) {
        $this->users = new ArrayCollection();
        $this->packs = new ArrayCollection();
        $this->group_packs = new ArrayCollection();
        $this->invites = new ArrayCollection();
        $this->coupons = new ArrayCollection();
        $this->roles = [];
        parent::__construct($name, $roles);
    }

    /**
     * Add users
     *
     * @param \StudySauce\Bundle\Entity\User $users
     * @return Group
     */
    public function addUser(\StudySauce\Bundle\Entity\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \StudySauce\Bundle\Entity\User $users
     */
    public function removeUser(\StudySauce\Bundle\Entity\User $users)
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

    /**
     * Add packs
     *
     * @param \StudySauce\Bundle\Entity\Pack $packs
     * @return Group
     */
    public function addPack(\StudySauce\Bundle\Entity\Pack $packs)
    {
        $this->packs[] = $packs;

        return $this;
    }

    /**
     * Remove packs
     *
     * @param \StudySauce\Bundle\Entity\Pack $packs
     */
    public function removePack(\StudySauce\Bundle\Entity\Pack $packs)
    {
        $this->packs->removeElement($packs);
    }

    /**
     * Get packs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPacks()
    {
        return new ArrayCollection(array_merge($this->packs->toArray(), $this->group_packs->toArray()));
    }

    /**
     * Set logo
     *
     * @param \StudySauce\Bundle\Entity\File $logo
     * @return Group
     */
    public function setLogo(\StudySauce\Bundle\Entity\File $logo = null)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo
     *
     * @return \StudySauce\Bundle\Entity\File 
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return Group
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
     * Add coupons
     *
     * @param \StudySauce\Bundle\Entity\Coupon $coupons
     * @return Group
     */
    public function addCoupon(\StudySauce\Bundle\Entity\Coupon $coupons)
    {
        $this->coupons[] = $coupons;

        return $this;
    }

    /**
     * Remove coupons
     *
     * @param \StudySauce\Bundle\Entity\Coupon $coupons
     */
    public function removeCoupon(\StudySauce\Bundle\Entity\Coupon $coupons)
    {
        $this->coupons->removeElement($coupons);
    }

    /**
     * Get coupons
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCoupons()
    {
        return $this->coupons;
    }

    /**
     * Add invites
     *
     * @param \StudySauce\Bundle\Entity\Invite $invites
     * @return Group
     */
    public function addInvite(\StudySauce\Bundle\Entity\Invite $invites)
    {
        $this->invites[] = $invites;

        return $this;
    }

    /**
     * Remove invites
     *
     * @param \StudySauce\Bundle\Entity\Invite $invites
     */
    public function removeInvite(\StudySauce\Bundle\Entity\Invite $invites)
    {
        $this->invites->removeElement($invites);
    }

    /**
     * Get invites
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInvites()
    {
        return $this->invites;
    }

    /**
     * Add group_packs
     *
     * @param \StudySauce\Bundle\Entity\Pack $groupPacks
     * @return Group
     */
    public function addGroupPack(\StudySauce\Bundle\Entity\Pack $groupPacks)
    {
        $this->group_packs[] = $groupPacks;

        return $this;
    }

    /**
     * Remove group_packs
     *
     * @param \StudySauce\Bundle\Entity\Pack $groupPacks
     */
    public function removeGroupPack(\StudySauce\Bundle\Entity\Pack $groupPacks)
    {
        $this->group_packs->removeElement($groupPacks);
    }

    /**
     * Get group_packs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGroupPacks()
    {
        return $this->group_packs;
    }
}
