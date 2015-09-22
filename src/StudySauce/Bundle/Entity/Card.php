<?php

namespace StudySauce\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="card")
 * @ORM\HasLifecycleCallbacks()
 */
class Card
{
    /**
     * @ORM\Column(type="integer", name="id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Pack", inversedBy="cards")
     * @ORM\JoinColumn(name="pack_id", referencedColumnName="id", nullable=true)
     */
    protected $pack;

    /**
     * @ORM\Column(type="datetime", name="created")
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime", name="modified", nullable=true)
     */
    protected $modified;

    /**
     * @ORM\Column(type="text", name="content")
     */
    protected $content;

    /**
     * @ORM\Column(type="text", name="response_content")
     */
    protected $responseContent;

    /**
     * @ORM\Column(type="string", length=16, name="content_type")
     */
    protected $contentType;

    /**
     * @ORM\Column(type="string", length=16, name="response_type")
     */
    protected $responseType;

    /**
     * @ORM\Column(type="string", length=16, name="recurrence")
     */
    protected $recurrence;

    /**
     * @ORM\PrePersist
     */
    public function setCreatedValue()
    {
        $this->created = new \DateTime();
    }
}