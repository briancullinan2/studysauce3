<?php

namespace StudySauce\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use WhiteOctober\SwiftMailerDBBundle\EmailInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="mail")
 * @ORM\HasLifecycleCallbacks()
 */
class Mail implements EmailInterface
{
    /**
     * @ORM\Column(type="integer", name="id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", name="status")
     */
    protected $status;

    /**
     * @ORM\Column(type="text", name="message")
     */
    protected $message;

    /**
     * @ORM\Column(type="string", length=256, name="environment")
     */
    protected $environment;

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
     * Set status
     *
     * @param integer $status
     * @return Mail
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return Mail
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }


    public function getObject()
    {
        if(!isset($this->object) && !empty($this->getMessage()))
            $this->object = unserialize($this->getMessage());
        return $this->object;
    }

    public function getTemplate()
    {
        /** @var \Swift_Message $message */
        $message = $this->getObject();
        /** @var \Swift_Mime_Headers_ParameterizedHeader $jsonStr */
        if(!empty($jsonStr = $message->getHeaders()->get('x-smtpapi'))) {
            $json = json_decode($jsonStr->getValue());
            if (isset($json->category)) {
                return $json->category[0];
            }
        }
        return null;
    }

    public function getRecipient()
    {
        /** @var \Swift_Message $message */
        $message = $this->getObject();
        $recipient = array_keys($message->getTo())[0];
        return $recipient;
    }

    public function getSender()
    {
        /** @var \Swift_Message $message */
        $message = $this->getObject();
        $recipient = array_keys($message->getFrom())[0];
        return $recipient;
    }

    /**
     * Set environment
     *
     * @param string $environment
     * @return Mail
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;

        return $this;
    }

    /**
     * Get environment
     *
     * @return string 
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Mail
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

}
