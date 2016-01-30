<?php

namespace StudySauce\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\Entity\Pack;


/**
 * @ORM\Entity
 * @ORM\Table(name="user_pack",uniqueConstraints={
 *     @ORM\UniqueConstraint(name="username_idx", columns={"user_id","pack_id"})})
 * @ORM\HasLifecycleCallbacks()
 */
class UserPack
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="User", inversedBy="userPacks", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Pack", inversedBy="userPacks", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="pack_id", referencedColumnName="id")
     */
    protected $pack;

    /** @ORM\Column(name="priority", type="decimal") */
    protected $priority = 0;

    /** @ORM\Column(name="retry_from", type="datetime", nullable=true) */
    protected $retryFrom;

    /** @ORM\Column(name="retry_to", type="datetime", nullable=true) */
    protected $retryTo;

    /**
     * @ORM\Column(type="datetime", name="downloaded", nullable=true)
     */
    protected $downloaded;

    /**
     * @ORM\Column(type="datetime", name="created")
     */
    protected $created;

    /**
     * @param int $correct
     * @return Response[]
     */
    public function getResponses(&$correct = 0)
    {
        $responses = [];
        $rids = [];
        $correct = 0;
        foreach($this->getUser()->getResponses()->toArray() as $r) {
            /** @var Response $r */
            if($r->getCard()->getPack()->getId() == $this->getPack()->getId()
                && !in_array($r->getCard()->getId(), $rids))
            {
                $rids[] = $r->getCard()->getId();
                $responses[] = $r;
                if($r->getCorrect()) {
                    $correct++;
                }
            }
        }
        return $responses;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedValue()
    {
        $this->created = new \DateTime();
    }


    /**
     * Set priority
     *
     * @param string $priority
     * @return UserPack
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
     * Set retryFrom
     *
     * @param \DateTime $retryFrom
     * @return UserPack
     */
    public function setRetryFrom($retryFrom)
    {
        $this->retryFrom = $retryFrom;

        return $this;
    }

    /**
     * Get retryFrom
     *
     * @return \DateTime 
     */
    public function getRetryFrom()
    {
        return $this->retryFrom;
    }

    /**
     * Set retryTo
     *
     * @param \DateTime $retryTo
     * @return UserPack
     */
    public function setRetryTo($retryTo)
    {
        $this->retryTo = $retryTo;

        return $this;
    }

    /**
     * Get retryTo
     *
     * @return \DateTime 
     */
    public function getRetryTo()
    {
        return $this->retryTo;
    }

    /**
     * Set downloaded
     *
     * @param \DateTime $downloaded
     * @return UserPack
     */
    public function setDownloaded($downloaded)
    {
        $this->downloaded = $downloaded;

        return $this;
    }

    /**
     * Get downloaded
     *
     * @return \DateTime 
     */
    public function getDownloaded()
    {
        return $this->downloaded;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return UserPack
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
     * Set user
     *
     * @param \StudySauce\Bundle\Entity\User $user
     * @return UserPack
     */
    public function setUser(\StudySauce\Bundle\Entity\User $user)
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
     * Set pack
     *
     * @param \StudySauce\Bundle\Entity\Pack $pack
     * @return UserPack
     */
    public function setPack(\StudySauce\Bundle\Entity\Pack $pack)
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

}
