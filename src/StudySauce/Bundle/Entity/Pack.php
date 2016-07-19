<?php

namespace StudySauce\Bundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\GroupInterface;
use StudySauce\Bundle\Entity\UserPack;

/**
 * @ORM\Entity
 * @ORM\Table(name="pack")
 * @ORM\HasLifecycleCallbacks()
 */
class Pack
{
    /**
     * @ORM\Column(type="integer", name="id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="packs")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", nullable=true)
     */
    protected $group;

    /**
     * @ORM\ManyToMany(targetEntity="Group")
     * @ORM\JoinTable(name="group_pack",
     *      joinColumns={@ORM\JoinColumn(name="pack_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")})
     */
    protected $groups;

    /**
     * @ORM\ManyToMany(targetEntity="Coupon", mappedBy="packs", fetch="EXTRA_LAZY")
     */
    protected $coupons;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="packs")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    protected $user;

    /**
     * @ORM\OneToMany(targetEntity="UserPack", mappedBy="pack", fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $userPacks;

    /** @ORM\Column(name="properties", type="array", nullable=true) */
    protected $properties = [];

    /** @ORM\Column(name="downloads", type="integer") */
    protected $downloads = 0;

    /** @ORM\Column(name="rating", type="decimal") */
    protected $rating = 0;

    /** @ORM\Column(name="priority", type="decimal") */
    protected $priority = 0;

    /**
     * @ORM\Column(type="datetime", name="active_from", nullable=true)
     */
    protected $activeFrom;

    /**
     * @ORM\Column(type="datetime", name="active_to", nullable=true)
     */
    protected $activeTo;

    /**
     * @ORM\Column(type="string", length=16, name="status")
     */
    protected $status = 'UNPUBLISHED';

    /**
     * @ORM\Column(type="decimal", name="price")
     */
    protected $price = 0;

    /**
     * @ORM\Column(type="text", name="title")
     */
    protected $title;

    /**
     * @ORM\Column(type="text", name="description")
     */
    protected $description = '';

    /**
     * @ORM\Column(type="simple_array", name="tags", nullable=true)
     */
    protected $tags;

    /**
     * @ORM\OneToMany(targetEntity="Card", mappedBy="pack")
     */
    protected $cards;

    /**
     * @ORM\Column(type="datetime", name="created")
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime", name="modified", nullable=true)
     */
    protected $modified;

    public function getCreator() {
        return !empty($this->getGroup())
            ? $this->getGroup()->getName()
            : (!empty($this->getUser())
                ? ($this->getUser()->getFirst() . ' ' . $this->getUser()->getLast())
                : '');
    }

    public function setLogo(File $logo = null) {
        $this->setProperty('logo', !empty($logo) ? $logo->getUrl() : null);
    }

    /**
     * @param $prop
     * @param $value
     */
    public function setProperty($prop, $value)
    {
        if($prop == 'schedule' && is_string($value)) {
            $value = new \DateTime($value);
        }
        $this->properties[$prop] = $value;
    }

    /**
     * @param $prop
     * @return null
     */
    public function getProperty($prop)
    {
        if(isset($this->properties[$prop])) {
            if($prop == 'schedule' && is_string($this->properties[$prop])) {
                return new \DateTime($this->properties[$prop]);
            }

            return $this->properties[$prop];
        }
        return null;
    }

    /**
     * @return ArrayCollection
     */
    public function getUsers() {
        $users = [];
        foreach(array_merge(!empty($this->getUser()) ? [$this->getUser()] : [], array_map(function (UserPack $up) {return $up->getUser();}, $this->userPacks->filter(function (UserPack $up) {return !$up->getRemoved();})->toArray())) as $u) {
            if(!in_array($u, $users)) {
                $users[] = $u;
            }
        }
        return new ArrayCollection($users);
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedValue()
    {
        $this->created = new \DateTime();
    }

    public function getLogo()
    {
        if (!empty($this->getProperty('logo'))) {
            return $this->getProperty('logo');
        }
        $group = $this->getGroup();

        $logo = !empty($group) && !empty($group->getLogo())
            ? $group->getLogo()->getUrl()
            : (!empty($this->getUser()) && !empty($this->getUser()->getPhoto())
                ? $this->getUser()->getPhoto()->getUrl()
                : '');
        return $logo;
    }

    public function isNewForChild(User $c)
    {
        return $this->userPacks->matching(Criteria::create()
            ->where(Criteria::expr()
                ->eq('user', $c))
            ->andWhere(Criteria::expr()
                ->neq('downloaded', null))
            ->andWhere(Criteria::expr()
                ->neq('removed', true)))
            ->count() == 0 ||
            $c->getResponsesForPack($this)->matching(Criteria::create()
                ->where(Criteria::expr()
                    ->lte('created', new \DateTime())))
                ->count() == 0;
    }

    public function getCardCount() {
        return $this->getCards()->filter(function (Card $c) {return !$c->getDeleted();})->count();
    }

    public function getChildUsers(User $user)
    {
        $this->userPacks->toArray();
        $this->groups->toArray();
        return array_filter(
        // also return current user and children
            array_merge([$user], $user->getInvites()
                ->filter(function (Invite $i) {
                    return !empty($i->getInvitee());
                })
                ->map(function (Invite $i) {
                    return $i->getInvitee();
                })->toArray()),
            function (User $u) {
                return ($this->getUser() == $u && $this->getStatus() != 'DELETED' && $this->getStatus() != 'GROUP')
                || $this->userPacks->matching(Criteria::create()
                    ->where(Criteria::expr()
                        // TODO: should this be or NULL?
                        ->neq('removed', true))
                    ->andWhere(Criteria::expr()
                        ->eq('user', $u)))->count() > 0
                || $this->groups->matching(Criteria::create()
                    ->where(Criteria::expr()
                        ->in('name', $u->getGroupNames())))->count() > 0 && $this->getStatus() == 'GROUP';
            });
    }

    public function getUserById($id)
    {
        /** @var UserPack $up */
        if(!empty($this->getUser()) && $this->getUser()->getId() == $id) {
            return $this->getUser();
        }
        $up = $this->userPacks->filter(function (UserPack $up) use ($id) {return $up->getUser()->getId() == $id;})->first();
        if(empty($up)) {
            return null;
        }
        return $up->getUser();
    }

    public function getGroupForChild(User $childUser)
    {
        $this->groups->toArray();
        return $this->groups->matching(Criteria::create()->where(Criteria::expr()->in('name', $childUser->getGroupNames())))->first();
    }

    /**
     * @param User $u
     * @return UserPack|null
     */
    public function getUserPack(User $u) {
        return $this->userPacks->matching(Criteria::create()->where(Criteria::expr()->eq('user', $u)))->first();
    }

    /**
     * @param int $uid
     * @return UserPack|null
     */
    public function getUserPackById($uid) {
        return $this->userPacks->filter(function (UserPack $up) use ($uid) {return $up->getUser()->getId() == $uid;})->first();
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->cards = new ArrayCollection();
        $this->userPacks = new ArrayCollection();
        $this->groups = new ArrayCollection();
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
     * Set properties
     *
     * @param array $properties
     * @return Pack
     */
    public function setProperties($properties)
    {
        foreach($properties as $k => $v) {
            $this->setProperty($k, $v);
        }

        return $this;
    }

    /**
     * Get properties
     *
     * @return array 
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Set activeFrom
     *
     * @param \DateTime $activeFrom
     * @return Pack
     */
    public function setActiveFrom($activeFrom)
    {
        $this->activeFrom = $activeFrom;

        return $this;
    }

    /**
     * Get activeFrom
     *
     * @return \DateTime 
     */
    public function getActiveFrom()
    {
        return $this->activeFrom;
    }

    /**
     * Set activeTo
     *
     * @param \DateTime $activeTo
     * @return Pack
     */
    public function setActiveTo($activeTo)
    {
        $this->activeTo = $activeTo;

        return $this;
    }

    /**
     * Get activeTo
     *
     * @return \DateTime 
     */
    public function getActiveTo()
    {
        return $this->activeTo;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Pack
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Pack
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Pack
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
     * Set tags
     *
     * @param string $tags
     * @return Pack
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get tags
     *
     * @return string 
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Pack
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
     * Set modified
     *
     * @param \DateTime $modified
     * @return Pack
     */
    public function setModified($modified)
    {
        $this->modified = $modified;

        return $this;
    }

    /**
     * Get modified
     *
     * @return \DateTime 
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * Set group
     *
     * @param \StudySauce\Bundle\Entity\Group $group
     * @return Pack
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


    public function getGroupNames()
    {
        $names = array();
        foreach ($this->groups->toArray() as $group) {
            $names[] = $group->getName();
        }

        return $names;
    }

    /**
     * @param string $name
     *
     * @return boolean
     */
    public function hasGroup($name)
    {
        return in_array($name, $this->getGroupNames());
    }

    /**
     * Add groups
     *
     * @param \StudySauce\Bundle\Entity\Group
     * @return User
     */
    public function addGroup(Group $group)
    {
        if(!$this->groups->contains($group)) {
            $this->groups[] = $group;
            $group->addGroupPack($this);
        }

        return $this;
    }

    /**
     * Remove groups
     *
     * @param \StudySauce\Bundle\Entity\Group
     * @return $this|\FOS\UserBundle\Model\GroupableInterface|void
     */
    public function removeGroup(Group $group)
    {
        $this->groups->removeElement($group);
        $group->removePack($this);
        if($this->group == $group) {
            $this->group = null;
        }
    }

    /**
     * Get groups
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGroups()
    {
        $groups = [];
        foreach(array_merge(!empty($this->getGroup()) ? [$this->getGroup()] : [], $this->groups->toArray()) as $g) {
            if(!in_array($g, $groups)) {
                $groups[] = $g;
            }
        }
        return new ArrayCollection($groups);
    }

    /**
     * Set user
     *
     * @param \StudySauce\Bundle\Entity\User $user
     * @return Pack
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
     * Add cards
     *
     * @param \StudySauce\Bundle\Entity\Card $cards
     * @return Pack
     */
    public function addCard(\StudySauce\Bundle\Entity\Card $cards)
    {
        $this->cards[] = $cards;

        return $this;
    }

    /**
     * Remove cards
     *
     * @param \StudySauce\Bundle\Entity\Card $cards
     */
    public function removeCard(\StudySauce\Bundle\Entity\Card $cards)
    {
        $this->cards->removeElement($cards);
    }

    /**
     * Get cards
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCards()
    {
        return $this->getStatus() == 'DELETED' ? new ArrayCollection([]) : $this->cards;
    }

    /**
     * Set downloads
     *
     * @param integer $downloads
     * @return Pack
     */
    public function setDownloads($downloads)
    {
        $this->downloads = $downloads;

        return $this;
    }

    /**
     * Get downloads
     *
     * @return integer 
     */
    public function getDownloads()
    {
        return $this->downloads;
    }

    /**
     * Set rating
     *
     * @param string $rating
     * @return Pack
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return string 
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set priority
     *
     * @param string $priority
     * @return Pack
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return string 
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set price
     *
     * @param string $price
     * @return Pack
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Add userPacks
     *
     * @param \StudySauce\Bundle\Entity\UserPack $userPack
     * @return Pack
     */
    public function addUserPack(\StudySauce\Bundle\Entity\UserPack $userPack)
    {
        $this->userPacks[] = $userPack;
        $userPack->setRemoved(false);
        return $this;
    }

    /**
     * Remove userPacks
     *
     * @param \StudySauce\Bundle\Entity\UserPack $userPack
     */
    public function removeUserPack(\StudySauce\Bundle\Entity\UserPack $userPack)
    {
        $userPack->setRemoved(true);
        //$this->userPacks->removeElement($userPacks);
    }

    /**
     * Get userPacks
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserPacks()
    {
        return $this->userPacks;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return Pack
     */
    public function setDeleted($deleted)
    {
        $this->setStatus($deleted ? 'DELETED' : 'UNPUBLISHED');

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean 
     */
    public function getDeleted()
    {
        return $this->getStatus() == 'DELETED';
    }



    /**
     * Add coupon
     *
     * @param \StudySauce\Bundle\Entity\Coupon $coupon
     *
     * @return Pack
     */
    public function addCoupon(\StudySauce\Bundle\Entity\Coupon $coupon)
    {
        $this->coupons[] = $coupon;

        return $this;
    }

    /**
     * Remove coupon
     *
     * @param \StudySauce\Bundle\Entity\Coupon $coupon
     */
    public function removeCoupon(\StudySauce\Bundle\Entity\Coupon $coupon)
    {
        $this->coupons->removeElement($coupon);
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
}
