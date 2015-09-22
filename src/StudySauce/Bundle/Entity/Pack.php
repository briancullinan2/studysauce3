<?php

namespace StudySauce\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="packs")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    protected $user;

    /** @ORM\Column(name="properties", type="array", nullable=true) */
    protected $properties = [];

    /** @ORM\Column(name="downloads", type="int") */
    protected $downloads = 0;

    /** @ORM\Column(name="rating", type="double") */
    protected $rating = 0;

    /** @ORM\Column(name="priority", type="double") */
    protected $priority = 0;

    /**
     * @ORM\Column(type="datetime", name="active_from")
     */
    protected $activeFrom;

    /**
     * @ORM\Column(type="datetime", name="active_to")
     */
    protected $activeTo;

    /**
     * @ORM\Column(type="string", length=16, name="status")
     */
    protected $status;

    /**
     * @ORM\Column(type="double", name="price")
     */
    protected $price;

    /**
     * @ORM\Column(type="text", name="title")
     */
    protected $title;

    /**
     * @ORM\Column(type="text", name="description")
     */
    protected $description;

    /**
     * @ORM\Column(type="text", name="tags")
     */
    protected $tags;

    /**
     * @ORM\OneToMany(targetEntity="Card", mappedBy="pack")
     * @ORM\OrderBy({"created" = "DESC"})
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

    /**
     * @ORM\PrePersist
     */
    public function setCreatedValue()
    {
        $this->created = new \DateTime();
    }

}