<?php

namespace StudySauce\Bundle\Entity;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use StudySauce\Bundle\Controller\PacksController;
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
     * @ORM\Column(type="array", name="retention", nullable=true)
     */
    protected $retention;

    /**
     * @ORM\Column(type="boolean", name="removed")
     */
    protected $removed = false;

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

    public function getRetention(&$refresh = false) {
        $intervals = [1, 2, 4, 7, 14, 28, 28 * 3, 28 * 6, 7 * 52];
        if(!empty($this->retention) && !$refresh) {
            return $this->retention;
        }
        $refresh = true;
        // if a card hasn't been answered, return the next card
        $cards = $this->getPack()->getCards()->filter(function (Card $c) {return !$c->getDeleted();})->toArray();
        $responses = $this->getUser()->getResponsesForPack($this->getPack());
        $result = [];
        foreach($cards as $c) {
            /** @var Card $c */
            /** @var Response[] $cardResponses */
            $cardResponses = $responses->matching(Criteria::create()->where(Criteria::expr()->eq('card', $c)))->toArray();
            usort($cardResponses, function (Response $r1, Response $r2) {return $r1->getCreated()->getTimestamp() - $r2->getCreated()->getTimestamp();});
            /** @var \DateTime $last */
            $last = null;
            $i = 0;
            $correctAfter = false;
            $max = null;
            foreach($cardResponses as $r) {
                if ($r->getCorrect()) {
                    // If it is in between time intervals ignore the response
                    while ($i < count($intervals) && ($last == null || date_time_set(clone $r->getCreated(), 3, 0, 0) >= date_time_set(date_add(clone $last, new \DateInterval('P' . $intervals[$i] . 'D')), 3, 0, 0))) {
                        // shift the time interval if answers correctly in the right time frame
                        $last = $r->getCreated();
                        $i += 1;
                    }
                    $correctAfter = true;
                }
                else {
                    $i = 0;
                    $last = $r->getCreated();
                    $correctAfter = false;
                }
                $max = $r->getCreated();
            }
            if ($i < 0) {
                $i = 0;
            }
            if ($i > count($intervals) - 1) {
                $i = count($intervals) - 1;
            }
            $result[$c->getId()] = [
                // interval value
                $intervals[$i],
                // last interval date
                !empty($last) ? $last->format('r') : null,
                // should display on home screen
                empty($last) || ($i == 0 && !$correctAfter) || date_add(date_time_set(clone $last, 3, 0, 0), new \DateInterval('P' . $intervals[$i] . 'D')) <= date_time_set(new \DateTime(), 3, 0, 0),
                // last response date for card, used for counting
                empty($max) ? null : $max->format('r')
            ];
        }
        $this->retention = $result;

        return $result;
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


    /**
     * Set removed
     *
     * @param boolean $removed
     *
     * @return UserPack
     */
    public function setRemoved($removed)
    {
        $this->removed = $removed;

        return $this;
    }

    /**
     * Get removed
     *
     * @return boolean
     */
    public function getRemoved()
    {
        return $this->removed;
    }

    /**
     * Set retention
     *
     * @param array $retention
     *
     * @return UserPack
     */
    public function setRetention($retention)
    {
        $this->retention = $retention;

        return $this;
    }
}
