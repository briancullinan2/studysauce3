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
    const SELF_ASSESSMENT = 'SELF_ASSESSMENT';
    const SHORT_ANSWER = 'SHORT_ANSWER';
    const MULTIPLE_CHOICE = 'MULTIPLE_CHOICE';

    /**
     * @ORM\Column(type="integer", name="id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Pack", inversedBy="cards")
     * @ORM\JoinColumn(name="pack_id", referencedColumnName="id")
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
     * @ORM\Column(type="text", name="response_content", nullable=true)
     */
    protected $responseContent;

    /**
     * @ORM\Column(type="string", length=16, name="content_type")
     */
    protected $contentType = ''; // default is TEXT

    /**
     * @ORM\Column(type="string", length=16, name="response_type")
     */
    protected $responseType = ''; // default is SELF_ASSESSMENT

    /**
     * @ORM\Column(type="string", length=16, name="recurrence")
     */
    protected $recurrence = ''; // default is 1 day, 2 day 4 day, 1 week, 2 week, 4 week

    /**
     * @ORM\OneToMany(targetEntity="Response", mappedBy="card", fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $responses;

    /**
     * @ORM\OneToMany(targetEntity="Answer", mappedBy="card")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $answers;

    /**
     * @ORM\Column(type="boolean", name="deleted")
     */
    protected $deleted = false;

    /**
     * @param User $user
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getResponsesForUser($user)
    {
        return $this->getResponses()->filter(function (Response $r) use ($user) {
            return $r->getUser() == $user;
        });
    }

    public function getIndex() {
        return !empty($this->getPack()) ? $this->getPack()->getCards()->indexOf($this) : 0;
    }

    /**
     * @return Answer
     */
    public function getCorrect()
    {
        return $this->getAnswers()->filter(function (Answer $a) {return $a->getCorrect() == 1 && !$a->getDeleted();})->first();
    }

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
     * Set created
     *
     * @param \DateTime $created
     * @return Card
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
     * @return Card
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
     * Set content
     *
     * @param string $content
     * @return Card
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
     * Set responseContent
     *
     * @param string $responseContent
     * @return Card
     */
    public function setResponseContent($responseContent)
    {
        $this->responseContent = $responseContent;

        return $this;
    }

    /**
     * Get responseContent
     *
     * @return string 
     */
    public function getResponseContent()
    {
        return $this->responseContent;
    }

    /**
     * Set contentType
     *
     * @param string $contentType
     * @return Card
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * Get contentType
     *
     * @return string 
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Set responseType
     *
     * @param string $responseType
     * @return Card
     */
    public function setResponseType($responseType)
    {
        $this->responseType = $responseType;

        return $this;
    }

    /**
     * Get responseType
     *
     * @return string 
     */
    public function getResponseType()
    {
        return $this->responseType;
    }

    /**
     * Set recurrence
     *
     * @param string $recurrence
     * @return Card
     */
    public function setRecurrence($recurrence)
    {
        $this->recurrence = $recurrence;

        return $this;
    }

    /**
     * Get recurrence
     *
     * @return string 
     */
    public function getRecurrence()
    {
        return $this->recurrence;
    }

    /**
     * Set pack
     *
     * @param \StudySauce\Bundle\Entity\Pack $pack
     * @return Card
     */
    public function setPack(\StudySauce\Bundle\Entity\Pack $pack = null)
    {
        $this->pack = $pack;

        return $this;
    }

    /**
     * Get pack
     *
     * @return \StudySauce\Bundle\Entity\Pack 
     */
    public function getPack()
    {
        return $this->pack;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->responses = new \Doctrine\Common\Collections\ArrayCollection();
        $this->answers = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add responses
     *
     * @param \StudySauce\Bundle\Entity\Response $responses
     * @return Card
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
     * Add answers
     *
     * @param \StudySauce\Bundle\Entity\Answer $answers
     * @return Card
     */
    public function addAnswer(\StudySauce\Bundle\Entity\Answer $answers)
    {
        $this->answers[] = $answers;

        return $this;
    }

    /**
     * Remove answers
     *
     * @param \StudySauce\Bundle\Entity\Answer $answers
     */
    public function removeAnswer(\StudySauce\Bundle\Entity\Answer $answers)
    {
        $this->answers->removeElement($answers);
    }

    /**
     * Get answers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return Card
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
