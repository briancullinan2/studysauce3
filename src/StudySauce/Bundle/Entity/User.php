<?php

namespace StudySauce\Bundle\Entity;

use Course1\Bundle\Course1Bundle;
use Course1\Bundle\Entity\Course1;
use Course2\Bundle\Course2Bundle;
use Course2\Bundle\Entity\Course2;
use Course3\Bundle\Course3Bundle;
use Course3\Bundle\Entity\Course3;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\GroupInterface;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
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
     * @ORM\OneToMany(targetEntity="Schedule", mappedBy="user", fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"term" = "DESC", "created" = "DESC"})
     */
    protected $schedules;

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
     * @ORM\OneToMany(targetEntity="Goal", mappedBy="user", fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $goals;

    /**
     * @ORM\OneToMany(targetEntity="Deadline", mappedBy="user", fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"dueDate" = "ASC"})
     */
    protected $deadlines;

    /**
     * @ORM\OneToMany(targetEntity="PartnerInvite", mappedBy="user", fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $partnerInvites;

    /**
     * @ORM\OneToMany(targetEntity="ParentInvite", mappedBy="user")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $parentInvites;

    /**
     * @ORM\OneToMany(targetEntity="StudentInvite", mappedBy="user")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $studentInvites;

    /**
     * @ORM\OneToMany(targetEntity="GroupInvite", mappedBy="user")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $groupInvites;

    /**
     * @ORM\OneToMany(targetEntity="PartnerInvite", mappedBy="partner")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $invitedPartners;

    /**
     * @ORM\OneToMany(targetEntity="ParentInvite", mappedBy="parent")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $invitedParents;

    /**
     * @ORM\OneToMany(targetEntity="StudentInvite", mappedBy="student")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $invitedStudents;

    /**
     * @ORM\OneToMany(targetEntity="GroupInvite", mappedBy="student")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $invitedGroups;

    /**
     * @ORM\OneToMany(targetEntity="File", mappedBy="user")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $files;

    /**
     * @ORM\OneToMany(targetEntity="StudyNote", mappedBy="user", fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $notes;

    /**
     * @ORM\OneToMany(targetEntity="ContactMessage", mappedBy="user", fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $messages;

    /**
     * @ORM\OneToMany(targetEntity="Course1\Bundle\Entity\Course1", mappedBy="user", fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $course1s;

    /**
     * @ORM\OneToMany(targetEntity="Course2\Bundle\Entity\Course2", mappedBy="user", fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $course2s;

    /**
     * @ORM\OneToMany(targetEntity="Course3\Bundle\Entity\Course3", mappedBy="user", fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $course3s;

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
     * @return PartnerInvite|User
     */
    public function getPartnerOrAdviser()
    {
        /** @var PartnerInvite $partner */
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
     * Add schedules
     *
     * @param \StudySauce\Bundle\Entity\Schedule $schedules
     * @return User
     */
    public function addSchedule(\StudySauce\Bundle\Entity\Schedule $schedules)
    {
        $this->schedules = new ArrayCollection(array_merge([$schedules], $this->schedules->toArray()));

        return $this;
    }

    /**
     * Remove schedules
     *
     * @param \StudySauce\Bundle\Entity\Schedule $schedules
     */
    public function removeSchedule(\StudySauce\Bundle\Entity\Schedule $schedules)
    {
        $this->schedules->removeElement($schedules);
    }

    /**
     * Get schedules
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSchedules()
    {
        return $this->schedules;
    }

    /**
     * Add payments
     *
     * @param \StudySauce\Bundle\Entity\Payment $payments
     * @return User
     */
    public function addPayment(\StudySauce\Bundle\Entity\Payment $payments)
    {
        $this->payments[] = $payments;

        return $this;
    }

    /**
     * Remove payments
     *
     * @param \StudySauce\Bundle\Entity\Payment $payments
     */
    public function removePayment(\StudySauce\Bundle\Entity\Payment $payments)
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
     * @param \StudySauce\Bundle\Entity\Visit $visits
     * @return User
     */
    public function addVisit(\StudySauce\Bundle\Entity\Visit $visits)
    {
        $this->visits[] = $visits;

        return $this;
    }

    /**
     * Remove visits
     *
     * @param \StudySauce\Bundle\Entity\Visit $visits
     */
    public function removeVisit(\StudySauce\Bundle\Entity\Visit $visits)
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
     * Add goals
     *
     * @param \StudySauce\Bundle\Entity\Goal $goals
     * @return User
     */
    public function addGoal(\StudySauce\Bundle\Entity\Goal $goals)
    {
        $this->goals[] = $goals;

        return $this;
    }

    /**
     * Remove goals
     *
     * @param \StudySauce\Bundle\Entity\Goal $goals
     */
    public function removeGoal(\StudySauce\Bundle\Entity\Goal $goals)
    {
        $this->goals->removeElement($goals);
    }

    /**
     * Get goals
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGoals()
    {
        return $this->goals;
    }

    /**
     * Add deadlines
     *
     * @param \StudySauce\Bundle\Entity\Deadline $deadlines
     * @return User
     */
    public function addDeadline(\StudySauce\Bundle\Entity\Deadline $deadlines)
    {
        $this->deadlines[] = $deadlines;

        return $this;
    }

    /**
     * Remove deadlines
     *
     * @param \StudySauce\Bundle\Entity\Deadline $deadlines
     */
    public function removeDeadline(\StudySauce\Bundle\Entity\Deadline $deadlines)
    {
        $this->deadlines->removeElement($deadlines);
    }

    /**
     * Get deadlines
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDeadlines()
    {
        return $this->deadlines;
    }

    /**
     * Add partnerInvites
     *
     * @param \StudySauce\Bundle\Entity\PartnerInvite $partnerInvites
     * @return User
     */
    public function addPartnerInvite(\StudySauce\Bundle\Entity\PartnerInvite $partnerInvites)
    {
        $this->partnerInvites[] = $partnerInvites;

        return $this;
    }

    /**
     * Remove partnerInvites
     *
     * @param \StudySauce\Bundle\Entity\PartnerInvite $partnerInvites
     */
    public function removePartnerInvite(\StudySauce\Bundle\Entity\PartnerInvite $partnerInvites)
    {
        $this->partnerInvites->removeElement($partnerInvites);
    }

    /**
     * Get partnerInvites
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPartnerInvites()
    {
        return $this->partnerInvites;
    }

    /**
     * Add parentInvites
     *
     * @param \StudySauce\Bundle\Entity\ParentInvite $parentInvites
     * @return User
     */
    public function addParentInvite(\StudySauce\Bundle\Entity\ParentInvite $parentInvites)
    {
        $this->parentInvites[] = $parentInvites;

        return $this;
    }

    /**
     * Remove parentInvites
     *
     * @param \StudySauce\Bundle\Entity\ParentInvite $parentInvites
     */
    public function removeParentInvite(\StudySauce\Bundle\Entity\ParentInvite $parentInvites)
    {
        $this->parentInvites->removeElement($parentInvites);
    }

    /**
     * Get parentInvites
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getParentInvites()
    {
        return $this->parentInvites;
    }

    /**
     * Add studentInvites
     *
     * @param StudentInvite $studentInvites
     * @return User
     */
    public function addStudentInvite(StudentInvite $studentInvites)
    {
        $this->studentInvites[] = $studentInvites;

        return $this;
    }

    /**
     * Remove studentInvites
     *
     * @param StudentInvite $studentInvites
     */
    public function removeStudentInvite(StudentInvite $studentInvites)
    {
        $this->studentInvites->removeElement($studentInvites);
    }

    /**
     * Get studentInvites
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getStudentInvites()
    {
        return $this->studentInvites;
    }

    /**
     * Add files
     *
     * @param \StudySauce\Bundle\Entity\File $files
     * @return User
     */
    public function addFile(\StudySauce\Bundle\Entity\File $files)
    {
        $this->files[] = $files;

        return $this;
    }

    /**
     * Remove files
     *
     * @param \StudySauce\Bundle\Entity\File $files
     */
    public function removeFile(\StudySauce\Bundle\Entity\File $files)
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
     * Add course1s
     *
     * @param \Course1\Bundle\Entity\Course1 $course1s
     * @return User
     */
    public function addCourse1(\Course1\Bundle\Entity\Course1 $course1s)
    {
        $this->course1s[] = $course1s;

        return $this;
    }

    /**
     * Remove course1s
     *
     * @param \Course1\Bundle\Entity\Course1 $course1s
     */
    public function removeCourse1(\Course1\Bundle\Entity\Course1 $course1s)
    {
        $this->course1s->removeElement($course1s);
    }

    /**
     * Get course1s
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCourse1s()
    {
        return $this->course1s;
    }

    /**
     * Set photo
     *
     * @param \StudySauce\Bundle\Entity\File $photo
     * @return User
     */
    public function setPhoto(\StudySauce\Bundle\Entity\File $photo = null)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return \StudySauce\Bundle\Entity\File 
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
     * Add groupInvites
     *
     * @param \StudySauce\Bundle\Entity\GroupInvite $groupInvites
     * @return User
     */
    public function addGroupInvite(\StudySauce\Bundle\Entity\GroupInvite $groupInvites)
    {
        $this->groupInvites[] = $groupInvites;

        return $this;
    }

    /**
     * Remove groupInvites
     *
     * @param \StudySauce\Bundle\Entity\GroupInvite $groupInvites
     */
    public function removeGroupInvite(\StudySauce\Bundle\Entity\GroupInvite $groupInvites)
    {
        $this->groupInvites->removeElement($groupInvites);
    }

    /**
     * Get groupInvites
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGroupInvites()
    {
        return $this->groupInvites;
    }

    /**
     * Add invitedPartners
     *
     * @param \StudySauce\Bundle\Entity\PartnerInvite $invitedPartners
     * @return User
     */
    public function addInvitedPartner(\StudySauce\Bundle\Entity\PartnerInvite $invitedPartners)
    {
        $this->invitedPartners[] = $invitedPartners;

        return $this;
    }

    /**
     * Remove invitedPartners
     *
     * @param \StudySauce\Bundle\Entity\PartnerInvite $invitedPartners
     */
    public function removeInvitedPartner(\StudySauce\Bundle\Entity\PartnerInvite $invitedPartners)
    {
        $this->invitedPartners->removeElement($invitedPartners);
    }

    /**
     * Get invitedPartners
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInvitedPartners()
    {
        return $this->invitedPartners;
    }

    /**
     * Add invitedParents
     *
     * @param \StudySauce\Bundle\Entity\ParentInvite $invitedParents
     * @return User
     */
    public function addInvitedParent(\StudySauce\Bundle\Entity\ParentInvite $invitedParents)
    {
        $this->invitedParents[] = $invitedParents;

        return $this;
    }

    /**
     * Remove invitedParents
     *
     * @param \StudySauce\Bundle\Entity\ParentInvite $invitedParents
     */
    public function removeInvitedParent(\StudySauce\Bundle\Entity\ParentInvite $invitedParents)
    {
        $this->invitedParents->removeElement($invitedParents);
    }

    /**
     * Get invitedParents
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInvitedParents()
    {
        return $this->invitedParents;
    }

    /**
     * Add invitedStudents
     *
     * @param \StudySauce\Bundle\Entity\StudentInvite $invitedStudents
     * @return User
     */
    public function addInvitedStudent(\StudySauce\Bundle\Entity\StudentInvite $invitedStudents)
    {
        $this->invitedStudents[] = $invitedStudents;

        return $this;
    }

    /**
     * Remove invitedStudents
     *
     * @param \StudySauce\Bundle\Entity\StudentInvite $invitedStudents
     */
    public function removeInvitedStudent(\StudySauce\Bundle\Entity\StudentInvite $invitedStudents)
    {
        $this->invitedStudents->removeElement($invitedStudents);
    }

    /**
     * Get invitedStudents
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInvitedStudents()
    {
        return $this->invitedStudents;
    }

    /**
     * Add invitedGroups
     *
     * @param \StudySauce\Bundle\Entity\GroupInvite $invitedGroups
     * @return User
     */
    public function addInvitedGroup(\StudySauce\Bundle\Entity\GroupInvite $invitedGroups)
    {
        $this->invitedGroups[] = $invitedGroups;

        return $this;
    }

    /**
     * Remove invitedGroups
     *
     * @param \StudySauce\Bundle\Entity\GroupInvite $invitedGroups
     */
    public function removeInvitedGroup(\StudySauce\Bundle\Entity\GroupInvite $invitedGroups)
    {
        $this->invitedGroups->removeElement($invitedGroups);
    }

    /**
     * Get invitedGroups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInvitedGroups()
    {
        return $this->invitedGroups;
    }

    /**
     * Add course2s
     *
     * @param \Course2\Bundle\Entity\Course2 $course2s
     * @return User
     */
    public function addCourse2(\Course2\Bundle\Entity\Course2 $course2s)
    {
        $this->course2s[] = $course2s;

        return $this;
    }

    /**
     * Remove course2s
     *
     * @param \Course2\Bundle\Entity\Course2 $course2s
     */
    public function removeCourse2(\Course2\Bundle\Entity\Course2 $course2s)
    {
        $this->course2s->removeElement($course2s);
    }

    /**
     * Get course2s
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCourse2s()
    {
        return $this->course2s;
    }

    /**
     * Add course3s
     *
     * @param \Course3\Bundle\Entity\Course3 $course3s
     * @return User
     */
    public function addCourse3(\Course3\Bundle\Entity\Course3 $course3s)
    {
        $this->course3s[] = $course3s;

        return $this;
    }

    /**
     * Remove course3s
     *
     * @param \Course3\Bundle\Entity\Course3 $course3s
     */
    public function removeCourse3(\Course3\Bundle\Entity\Course3 $course3s)
    {
        $this->course3s->removeElement($course3s);
    }

    /**
     * Get course3s
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCourse3s()
    {
        return $this->course3s;
    }

    /**
     * Add messages
     *
     * @param \StudySauce\Bundle\Entity\ContactMessage $messages
     * @return User
     */
    public function addMessage(\StudySauce\Bundle\Entity\ContactMessage $messages)
    {
        $this->messages[] = $messages;

        return $this;
    }

    /**
     * Remove messages
     *
     * @param \StudySauce\Bundle\Entity\ContactMessage $messages
     */
    public function removeMessage(\StudySauce\Bundle\Entity\ContactMessage $messages)
    {
        $this->messages->removeElement($messages);
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMessages()
    {
        return $this->messages;
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
     * Add notes
     *
     * @param \StudySauce\Bundle\Entity\StudyNote $notes
     * @return User
     */
    public function addNote(\StudySauce\Bundle\Entity\StudyNote $notes)
    {
        $this->notes[] = $notes;

        return $this;
    }

    /**
     * Remove notes
     *
     * @param \StudySauce\Bundle\Entity\StudyNote $notes
     */
    public function removeNote(\StudySauce\Bundle\Entity\StudyNote $notes)
    {
        $this->notes->removeElement($notes);
    }

    /**
     * Get notes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNotes()
    {
        return $this->notes;
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
