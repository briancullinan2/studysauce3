<?php

namespace StudySauce\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="response")
 * @ORM\HasLifecycleCallbacks()
 */
class Response
{
    /**
     * @ORM\Column(type="integer", name="id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime", name="created")
     */
    protected $created;

    /**
     * @ORM\ManyToOne(targetEntity="Card", inversedBy="responses")
     * @ORM\JoinColumn(name="card_id", referencedColumnName="id", nullable=true)
     */
    protected $card;

    /**
     * @ORM\ManyToOne(targetEntity="Answer", inversedBy="responses")
     * @ORM\JoinColumn(name="answer_id", referencedColumnName="id", nullable=true)
     */
    protected $answer;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="responses")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\OneToOne(targetEntity="File", inversedBy="response")
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id", nullable=true)
     */
    protected $file;

    /**
     * @ORM\Column(type="text", name="value", nullable=true)
     */
    protected $value;

    /**
     * @ORM\Column(type="boolean", name="correct")
     */
    protected $correct;


    /**
     * @ORM\PrePersist
     */
    public function setCreatedValue()
    {
        if($this->created == null) {
            $this->created = new \DateTime();
        }
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
     * @return Response
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
     * Set value
     *
     * @param string $value
     * @return Response
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
     * Set answer
     *
     * @param \StudySauce\Bundle\Entity\Answer $answer
     * @return Response
     */
    public function setAnswer(\StudySauce\Bundle\Entity\Answer $answer = null)
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * Get answer
     *
     * @return \StudySauce\Bundle\Entity\Answer 
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * Set user
     *
     * @param \StudySauce\Bundle\Entity\User $user
     * @return Response
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
     * Set file
     *
     * @param \StudySauce\Bundle\Entity\File $file
     * @return Response
     */
    public function setFile(\StudySauce\Bundle\Entity\File $file = null)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return \StudySauce\Bundle\Entity\File 
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set correct
     *
     * @param boolean $correct
     * @return Response
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
     * @return Response
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
}
