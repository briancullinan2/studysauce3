<?php

namespace StudySauce\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="answer")
 * @ORM\HasLifecycleCallbacks()
 */
class Answer
{
    /**
     * @ORM\Column(type="integer", name="id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Card", inversedBy="answers")
     * @ORM\JoinColumn(name="card_id", referencedColumnName="id")
     */
    protected $card;

    /**
     * @ORM\Column(type="text", name="content")
     */
    protected $content;

    /**
     * @ORM\Column(type="text", name="response")
     */
    protected $response;

    /**
     * @ORM\Column(type="text", name="value")
     */
    protected $value;

    /**
     * @ORM\Column(type="boolean", name="correct")
     */
    protected $correct = false;

    /**
     * @ORM\OneToMany(targetEntity="Response", mappedBy="answer")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $responses;

    /**
     * @ORM\Column(type="datetime", name="created")
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime", name="modified", nullable=true)
     */
    protected $modified;

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
     * Constructor
     */
    public function __construct()
    {
        $this->responses = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set content
     *
     * @param string $content
     * @return Answer
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set response
     *
     * @param string $response
     * @return Answer
     */
    public function setResponse($response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Get response
     *
     * @return string 
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return Answer
     */
    public function setValue($value)
    {
        $this->value = $value;
        if(empty($this->content)) {
            $this->content = str_replace('|', ' or ', rtrim(ltrim($value, '^'), '$'));
        }
        if(empty($this->response)) {
            $this->response = str_replace('|', ' or ', rtrim(ltrim($value, '^'), '$'));
        }

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
     * Set created
     *
     * @param \DateTime $created
     * @return Answer
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
     * @return Answer
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
     * Add responses
     *
     * @param \StudySauce\Bundle\Entity\Response $responses
     * @return Answer
     */
    public function addResponse(\StudySauce\Bundle\Entity\Response $responses)
    {
        $this->responses[] = $responses;

        return $this;
    }

    /**
     * Remove responses
     *
     * @param \StudySauce\Bundle\Entity\Response $responses
     */
    public function removeResponse(\StudySauce\Bundle\Entity\Response $responses)
    {
        $this->responses->removeElement($responses);
    }

    /**
     * Get responses
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getResponses()
    {
        return $this->responses;
    }

    /**
     * Set correct
     *
     * @param boolean $correct
     * @return Answer
     */
    public function setCorrect($correct)
    {
        $this->correct = $correct;

        return $this;
    }

    /**
     * Get correct
     *
     * @return boolean 
     */
    public function getCorrect()
    {
        return $this->correct;
    }

    /**
     * Set card
     *
     * @param \StudySauce\Bundle\Entity\Card $card
     * @return Answer
     */
    public function setCard(\StudySauce\Bundle\Entity\Card $card = null)
    {
        $this->card = $card;

        return $this;
    }

    /**
     * Get card
     *
     * @return \StudySauce\Bundle\Entity\Card 
     */
    public function getCard()
    {
        return $this->card;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return Answer
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
}
