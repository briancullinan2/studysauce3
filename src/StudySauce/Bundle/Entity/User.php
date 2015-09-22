<?php

namespace StudySauce\Bundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\GroupInterface;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use StudySauce\Bundle\Entity\Deadline;
use StudySauce\Bundle\Entity\File;
use StudySauce\Bundle\Entity\Payment;
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
    protected $packs;

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
     * @return float
     */
    public function getCompleted() {

        /** @var Course1 $course1 */
        $course1 = $this->getCourse1s()->slice(0, 1);
        if(!empty($course1)) $course1 = $course1[0];
        /** @var Course2 $course2 */
        $course2 = $this->getCourse2s()->slice(0, 1);
        if(!empty($course2)) $course2 = $course2[0];
        /** @var Course3 $course3 */
        $course3 = $this->getCourse3s()->slice(0, 1);
        if(!empty($course3)) $course3 = $course3[0];
        $completed = 0;
        if (!empty($course1)) {
            $completed += ($course1->getLesson1() === 4 ? 1 : 0) + ($course1->getLesson2() === 4 ? 1 : 0) +
                ($course1->getLesson3() === 4 ? 1 : 0) + ($course1->getLesson4() === 4 ? 1 : 0) +
                ($course1->getLesson5() === 4 ? 1 : 0) + ($course1->getLesson6() === 4 ? 1 : 0) +
                ($course1->getLesson7() === 4 && !$this->hasRole('ROLE_PAID') ? 1 : 0);
        }
        if (!empty($course2)) {
            $completed += ($course2->getLesson1() === 4 ? 1 : 0) + ($course2->getLesson2() === 4 ? 1 : 0) +
                ($course2->getLesson3() === 4 ? 1 : 0) + ($course2->getLesson4() === 4 ? 1 : 0) +
                ($course2->getLesson5() === 4 ? 1 : 0);
        }
        if (!empty($course3)) {
            $completed += ($course3->getLesson1() === 4 ? 1 : 0) + ($course3->getLesson2() === 4 ? 1 : 0) +
                ($course3->getLesson3() === 4 ? 1 : 0) + ($course3->getLesson4() === 4 ? 1 : 0) +
                ($course3->getLesson5() === 4 ? 1 : 0);
        }
        $overall = round(
            $completed * 100.0 / (Course1Bundle::COUNT_LEVEL
                - ($this->hasRole('ROLE_PAID') ? 1 : 0)
                + Course2Bundle::COUNT_LEVEL + Course3Bundle::COUNT_LEVEL)
        );

        return $overall;
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

        if($this->getSalt()[0] == '$' && $this->getSalt()[2] == '$') {
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

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->schedules = new \Doctrine\Common\Collections\ArrayCollection();
        $this->visits = new \Doctrine\Common\Collections\ArrayCollection();
        $this->goals = new \Doctrine\Common\Collections\ArrayCollection();
        $this->deadlines = new \Doctrine\Common\Collections\ArrayCollection();
        $this->files = new \Doctrine\Common\Collections\ArrayCollection();
        $this->course1s = new \Doctrine\Common\Collections\ArrayCollection();
        $this->course2s = new \Doctrine\Common\Collections\ArrayCollection();
        $this->course3s = new \Doctrine\Common\Collections\ArrayCollection();
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
        $this->messages = new \Doctrine\Common\Collections\ArrayCollection();
        $this->payments = new \Doctrine\Common\Collections\ArrayCollection();
        $this->partnerInvites = new \Doctrine\Common\Collections\ArrayCollection();
        $this->parentInvites = new \Doctrine\Common\Collections\ArrayCollection();
        $this->studentInvites = new \Doctrine\Common\Collections\ArrayCollection();
        $this->groupInvites = new \Doctrine\Common\Collections\ArrayCollection();
        $this->invitedParents = new \Doctrine\Common\Collections\ArrayCollection();
        $this->invitedPartners = new \Doctrine\Common\Collections\ArrayCollection();
        $this->invitedStudents = new \Doctrine\Common\Collections\ArrayCollection();
        $this->invitedGroups = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add groups
     *
     * @param \StudySauce\Bundle\Entity\Group $groups
     * @return User
     */
    public function addGroup(GroupInterface $groups)
    {
        $this->groups[] = $groups;

        return $this;
    }

    /**
     * Remove groups
     *
     * @param \StudySauce\Bundle\Entity\Group $groups
     */
    public function removeGroup(GroupInterface $groups)
    {
        $this->groups->removeElement($groups);
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
}
