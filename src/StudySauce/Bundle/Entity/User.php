<?php

namespace StudySauce\Bundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\GroupInterface;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use StudySauce\Bundle\Entity\File;
use StudySauce\Bundle\Entity\Invite;
use StudySauce\Bundle\Entity\Payment;
use StudySauce\Bundle\Entity\Response;
use StudySauce\Bundle\Entity\UserPack;
use StudySauce\Bundle\Entity\Visit;
use Symfony\Component\Security\Core\Encoder\EncoderAwareInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="ss_user",uniqueConstraints={
 *     @ORM\UniqueConstraint(name="email_idx", columns={"email"}),
 *     @ORM\UniqueConstraint(name="username_idx", columns={"username"})})
 * @ORM\HasLifecycleCallbacks()
 * @ORM\NamedNativeQueries({
 *      @ORM\NamedNativeQuery(
 *          name            = "sessionCount",
 *          resultSetMapping= "mappingSessionCount",
 *          query           = "SELECT COUNT(*) AS sessions FROM ss_user INNER JOIN visit ON id = user_id GROUP BY session_id"
 *      )
 * })
 * @ORM\SqlResultSetMappings({
 *      @ORM\SqlResultSetMapping(
 *          name    = "mappingSessionCount",
 *          columns = {
 *              @ORM\ColumnResult("sessions")
 *          }
 *      )
 * })
 */
class User extends BaseUser implements EncoderAwareInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Payment", mappedBy="user")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $payments;

    /**
     * @ORM\OneToMany(targetEntity="Visit", mappedBy="user", fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $visits;

    /**
     * @ORM\OneToMany(targetEntity="Invite", mappedBy="user")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $invites;

    /**
     * @ORM\OneToMany(targetEntity="Invite", mappedBy="invitee")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $invitees;

    /**
     * @ORM\OneToMany(targetEntity="Pack", mappedBy="user")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $authored;

    /**
     * @ORM\OneToMany(targetEntity="UserPack", mappedBy="user", fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $userPacks;

    /**
     * @ORM\OneToMany(targetEntity="File", mappedBy="user")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $files;

    /**
     * @ORM\OneToMany(targetEntity="Response", mappedBy="user")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $responses;

    /**
     * @ORM\Column(type="datetime", name="created")
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime", name="last_visit", nullable = true)
     */
    protected $lastVisit;

    /**
     * @ORM\Column(type="string", length=256, name="first")
     */
    protected $first = '';

    /**
     * @ORM\Column(type="string", length=256, name="last")
     */
    protected $last = '';

    /**
     * @ORM\OneToOne(targetEntity="File")
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id", nullable = true)
     */
    protected $photo;

    /** @ORM\Column(name="facebook_id", type="string", length=255, nullable=true) */
    protected $facebook_id;

    /** @ORM\Column(name="facebook_access_token", type="string", length=255, nullable=true) */
    protected $facebook_access_token;

    /** @ORM\Column(name="google_id", type="string", length=255, nullable=true) */
    protected $google_id;

    /** @ORM\Column(name="google_access_token", type="string", length=255, nullable=true) */
    protected $google_access_token;

    /** @ORM\Column(name="evernote_id", type="string", length=255, nullable=true) */
    protected $evernote_id;

    /** @ORM\Column(name="evernote_access_token", type="string", length=255, nullable=true) */
    protected $evernote_access_token;

    /** @ORM\Column(name="gcal_id", type="string", length=255, nullable=true) */
    protected $gcal_id;

    /** @ORM\Column(name="gcal_access_token", type="string", length=255, nullable=true) */
    protected $gcal_access_token;

    /**
     * @ORM\ManyToMany(targetEntity="Group")
     * @ORM\JoinTable(name="ss_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")})
     */
    protected $groups;

    /** @ORM\Column(name="properties", type="array", nullable=true) */
    protected $properties = [];

    /**
     * @ORM\PrePersist
     */
    public function setCreatedValue()
    {
        $this->created = new \DateTime();
    }

    /**
     * @return Invite|User
     */
    public function getPartnerOrAdviser()
    {
        /** @var Invite $partner */
        $partner = $this->getPartnerInvites()->first();
        $advisers = array_values($this->getGroups()
                ->map(function (Group $g) {return $g->getUsers()->filter(function (User $u) {
                    return $u->hasRole('ROLE_ADVISER');})->toArray();})
                ->filter(function ($c) {return !empty($c);})
                ->toArray());
        if(count($advisers) > 1)
            $advisers = call_user_func_array('array_merge', $advisers);
        elseif(count($advisers) > 0)
            $advisers = $advisers[0];
        usort($advisers, function (User $a, User $b) {return $a->hasRole('ROLE_MASTER_ADVISER') - $b->hasRole('ROLE_MASTER_ADVISER');});
        /** @var User $adviser */
        $adviser = reset($advisers);
        return !empty($adviser) ? $adviser : $partner;
    }

    /**
     * @return null|string
     */
    public function getEncoderName() {

        if(!empty($this->getSalt()) && $this->getSalt()[0] == '$' && $this->getSalt()[2] == '$') {
            return 'drupal_encoder';
        }

        return NULL;
    }

    /**
     * @param $prop
     * @param $value
     */
    public function setProperty($prop, $value)
    {
        $this->properties[$prop] = $value;
        $this->setProperties($this->properties);
    }

    /**
     * @param $prop
     * @return null
     */
    public function getProperty($prop)
    {
        if(isset($this->properties[$prop]))
            return $this->properties[$prop];
        return null;
    }

    public function getUsers() {
        $users = [];
        foreach($this->userPacks->toArray() as $u) {
            /** @var UserPack $u */
            $users[] = $u->getUser();
        }
        return $users;
    }

    /**
     * @return Pack[]
     */
    public function getPacks()
    {
        $packs = [];
        foreach($this->getAuthored()->toArray() as $p) {
            $packs[] = $p;
        }
        foreach($this->getUserPacks()->toArray() as $u) {
            /** @var UserPack $u */
            $packs[] = $u->getPack();
        }
        return $packs;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->schedules = new ArrayCollection();
        $this->visits = new ArrayCollection();
        $this->files = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->invites = new ArrayCollection();
        $this->responses = new ArrayCollection();
        $this->invitees = new ArrayCollection();
        $this->userPacks = new ArrayCollection();
        $this->authored = new ArrayCollection();
        parent::__construct();
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
     * @return User
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
     * Set first
     *
     * @param string $first
     * @return User
     */
    public function setFirst($first)
    {
        $this->first = $first;

        return $this;
    }

    /**
     * Get first
     *
     * @return string 
     */
    public function getFirst()
    {
        return $this->first;
    }

    /**
     * Set last
     *
     * @param string $last
     * @return User
     */
    public function setLast($last)
    {
        $this->last = $last;

        return $this;
    }

    /**
     * Get last
     *
     * @return string 
     */
    public function getLast()
    {
        return $this->last;
    }

    /**
     * Set facebook_id
     *
     * @param string $facebookId
     * @return User
     */
    public function setFacebookId($facebookId)
    {
        $this->facebook_id = $facebookId;

        return $this;
    }

    /**
     * Get facebook_id
     *
     * @return string 
     */
    public function getFacebookId()
    {
        return $this->facebook_id;
    }

    /**
     * Set facebook_access_token
     *
     * @param string $facebookAccessToken
     * @return User
     */
    public function setFacebookAccessToken($facebookAccessToken)
    {
        $this->facebook_access_token = $facebookAccessToken;

        return $this;
    }

    /**
     * Get facebook_access_token
     *
     * @return string 
     */
    public function getFacebookAccessToken()
    {
        return $this->facebook_access_token;
    }

    /**
     * Set google_id
     *
     * @param string $googleId
     * @return User
     */
    public function setGoogleId($googleId)
    {
        $this->google_id = $googleId;

        return $this;
    }

    /**
     * Get google_id
     *
     * @return string 
     */
    public function getGoogleId()
    {
        return $this->google_id;
    }

    /**
     * Set google_access_token
     *
     * @param string $googleAccessToken
     * @return User
     */
    public function setGoogleAccessToken($googleAccessToken)
    {
        $this->google_access_token = $googleAccessToken;

        return $this;
    }

    /**
     * Get google_access_token
     *
     * @return string 
     */
    public function getGoogleAccessToken()
    {
        return $this->google_access_token;
    }

    /**
     * Set properties
     *
     * @param string $properties
     * @return User
     */
    public function setProperties($properties)
    {
        $this->properties = $properties;

        return $this;
    }

    /**
     * Get properties
     *
     * @return string 
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Add payments
     *
     * @param Payment $payments
     * @return User
     */
    public function addPayment(Payment $payments)
    {
        $this->payments[] = $payments;

        return $this;
    }

    /**
     * Remove payments
     *
     * @param Payment $payments
     */
    public function removePayment(Payment $payments)
    {
        $this->payments->removeElement($payments);
    }

    /**
     * Get payments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPayments()
    {
        return $this->payments;
    }

    /**
     * Add visits
     *
     * @param Visit $visits
     * @return User
     */
    public function addVisit(Visit $visits)
    {
        $this->visits[] = $visits;

        return $this;
    }

    /**
     * Remove visits
     *
     * @param Visit $visits
     */
    public function removeVisit(Visit $visits)
    {
        $this->visits->removeElement($visits);
    }

    /**
     * Get visits
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getVisits()
    {
        return $this->visits;
    }

    /**
     * Add files
     *
     * @param File $files
     * @return User
     */
    public function addFile(File $files)
    {
        $this->files[] = $files;

        return $this;
    }

    /**
     * Remove files
     *
     * @param File $files
     */
    public function removeFile(File $files)
    {
        $this->files->removeElement($files);
    }

    /**
     * Get files
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Set photo
     *
     * @param File $photo
     * @return User
     */
    public function setPhoto(File $photo = null)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return File
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Set evernote_id
     *
     * @param string $evernoteId
     * @return User
     */
    public function setEvernoteId($evernoteId)
    {
        $this->evernote_id = $evernoteId;

        return $this;
    }

    /**
     * Get evernote_id
     *
     * @return string 
     */
    public function getEvernoteId()
    {
        return $this->evernote_id;
    }

    /**
     * Set evernote_access_token
     *
     * @param string $evernoteAccessToken
     * @return User
     */
    public function setEvernoteAccessToken($evernoteAccessToken)
    {
        $this->evernote_access_token = $evernoteAccessToken;

        return $this;
    }

    /**
     * Get evernote_access_token
     *
     * @return string 
     */
    public function getEvernoteAccessToken()
    {
        return $this->evernote_access_token;
    }

    /**
     * Set gcal_id
     *
     * @param string $gcalId
     * @return User
     */
    public function setGcalId($gcalId)
    {
        $this->gcal_id = $gcalId;

        return $this;
    }

    /**
     * Get gcal_id
     *
     * @return string 
     */
    public function getGcalId()
    {
        return $this->gcal_id;
    }

    /**
     * Set gcal_access_token
     *
     * @param string $gcalAccessToken
     * @return User
     */
    public function setGcalAccessToken($gcalAccessToken)
    {
        $this->gcal_access_token = $gcalAccessToken;

        return $this;
    }

    /**
     * Get gcal_access_token
     *
     * @return string 
     */
    public function getGcalAccessToken()
    {
        return $this->gcal_access_token;
    }

    /**
     * Set lastVisit
     *
     * @param \DateTime $lastVisit
     * @return User
     */
    public function setLastVisit($lastVisit)
    {
        $this->lastVisit = $lastVisit;

        return $this;
    }

    /**
     * Get lastVisit
     *
     * @return \DateTime 
     */
    public function getLastVisit()
    {
        return $this->lastVisit;
    }

    /**
     * Add invites
     *
     * @param Invite $invites
     * @return User
     */
    public function addInvite(Invite $invites)
    {
        $this->invites[] = $invites;

        return $this;
    }

    /**
     * Remove invites
     *
     * @param Invite $invites
     */
    public function removeInvite(Invite $invites)
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
     * Add invitees
     *
     * @param Invite $invitees
     * @return User
     */
    public function addInvitee(Invite $invitees)
    {
        $this->invitees[] = $invitees;

        return $this;
    }

    /**
     * Remove invitees
     *
     * @param Invite $invitees
     */
    public function removeInvitee(Invite $invitees)
    {
        $this->invitees->removeElement($invitees);
    }

    /**
     * Get invitees
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInvitees()
    {
        return $this->invitees;
    }

    /**
     * Add responses
     *
     * @param Response $responses
     * @return User
     */
    public function addResponse(Response $responses)
    {
        $this->responses[] = $responses;

        return $this;
    }

    /**
     * Remove responses
     *
     * @param Response $responses
     */
    public function removeResponse(Response $responses)
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
     * Add authored
     *
     * @param \StudySauce\Bundle\Entity\Pack $authored
     * @return User
     */
    public function addAuthored(\StudySauce\Bundle\Entity\Pack $authored)
    {
        $this->authored[] = $authored;

        return $this;
    }

    /**
     * Remove authored
     *
     * @param \StudySauce\Bundle\Entity\Pack $authored
     */
    public function removeAuthored(\StudySauce\Bundle\Entity\Pack $authored)
    {
        $this->authored->removeElement($authored);
    }

    /**
     * Get authored
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAuthored()
    {
        return $this->authored;
    }

    /**
     * Add userPacks
     *
     * @param UserPack $userPacks
     * @return User
     */
    public function addUserPack(UserPack $userPacks)
    {
        $this->userPacks[] = $userPacks;

        return $this;
    }

    /**
     * Remove userPacks
     *
     * @param UserPack $userPacks
     */
    public function removeUserPack(UserPack $userPacks)
    {
        $this->userPacks->removeElement($userPacks);
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
     * Add groups
     *
     * @param GroupInterface $group
     * @return User
     * @internal param Group $groups
     */
    public function addGroup(GroupInterface $group)
    {
        return parent::addGroup($group);
    }

    /**
     * Remove groups
     *
     * @param GroupInterface $group
     * @return $this|\FOS\UserBundle\Model\GroupableInterface|void
     * @internal param Group $groups
     */
    public function removeGroup(GroupInterface $group)
    {
        parent::removeGroup($group);
    }

    /**
     * Get groups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGroups()
    {
        return $this->groups;
    }
}
