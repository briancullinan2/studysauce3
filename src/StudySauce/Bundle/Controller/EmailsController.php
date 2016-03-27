<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\ORM\EntityManager;
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\ContactMessage;
use StudySauce\Bundle\Entity\Deadline;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\GroupInvite;
use StudySauce\Bundle\Entity\Invite;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\ParentInvite;
use StudySauce\Bundle\Entity\PartnerInvite;
use StudySauce\Bundle\Entity\Payment;
use StudySauce\Bundle\Entity\StudentInvite;
use StudySauce\Bundle\Entity\User;
use Swift_Message;
use Swift_Mime_Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class EmailsController
 * @package StudySauce\Bundle\Controller
 */
class EmailsController extends Controller
{
    /**
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function welcomePartnerAction(User $user = null)
    {
        /** @var $user User */
        if(empty($user))
            $user = $this->getUser();

        /** @var Swift_Mime_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject('Welcome to Study Sauce')
            ->setFrom('admin@studysauce.com')
            ->setTo($user->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:welcome-partner.html.php', [
                        'name' => $user,
                        'greeting' => (empty($user->getFirst()) ? 'Howdy partner' : ('Dear ' . $user->getFirst())) . ','
                    ]), 'text/html');
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode(['category' => ['welcome-partner']])));
        $this->send($message);

        return new Response();
    }

    /**
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function welcomeParentAction(User $user = null)
    {
        /** @var $user User */
        if(empty($user))
            $user = $this->getUser();

        /** @var Invite $groupInvite */
        $groupInvite = $user->getInvites()->filter(function (Invite $i) {return !empty($i->getInvitee()) && $i->getInvitee()->getGroups()->count() > 0;})->first();

        /** @var Group $group */
        if (!empty($groupInvite)) {
            $group = $groupInvite->getInvitee()->getGroups()->first();
        }

        /** @var Swift_Mime_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject((!empty($group) ? ($group->getDescription() . ' + ') : '') . 'Study Sauce welcomes you!')
            ->setFrom('admin@studysauce.com')
            ->setTo($user->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:welcome-parent.html.php', [
                'link' => false,
                'group' => !empty($group) ? $group->getDescription() : '',
                'groupLogo' => !empty($group->getLogo()) ? $group->getLogo()->getUrl() : '',
                'child' => !empty($groupInvite) ? $groupInvite->getInvitee()->getFirst() : '',
                'greeting' => (empty($user->getFirst()) ? 'Howdy partner' : ('Hello ' . $user->getFirst())) . ','
            ]), 'text/html');
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
            'category' => ['welcome-parent']])));
        $this->send($message);

        return new Response();
    }

    /**
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function welcomeStudentAction(User $user = null)
    {
        /** @var $user User */
        if(empty($user))
            $user = $this->getUser();

        /** @var Swift_Mime_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject('Welcome to Study Sauce')
            ->setFrom('admin@studysauce.com')
            ->setTo($user->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:welcome-student.html.php', [
                        'name' => $user,
                        'greeting' => 'Dear ' . ($user->getFirst() ?: 'student') . ','
                    ]), 'text/html');
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
            'category' => ['welcome-student']])));
        $this->send($message);

        return new Response();
    }

    /**
     * @param User $user
     * @param Pack[] $notify
     * @return Response
     */
    public function sendNewPacksNotification(User $user = null, $notify, $child)
    {
        /** @var $user User */
        if(empty($user))
            $user = $this->getUser();

        /** @var Invite $groupInvite */
        $groupInvite = $user->getInvites()->filter(function (Invite $i) {return !empty($i->getInvitee()) && $i->getInvitee()->getGroups()->count() > 0;})->first();

        /** @var Group $group */
        if (!empty($groupInvite)) {
            $group = $groupInvite->getInvitee()->getGroups()->first();
        }

        /** @var \Swift_Mime_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject(!empty($group) ? ($group->getDescription() . ' has added ' . $notify[0]->getTitle() . ' to Study Sauce') : 'We have added ' . $notify[0]->getTitle() . ' to Study Sauce')
            ->setFrom(!empty($user) ? $user->getEmail() : 'guest@studysauce.com')
            ->setTo($user->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:new-pack-notification.html.php', [
                'greeting' => 'Hello ' . $user->getFirst() . ',',
                'child' => $child,
                'group' => !empty($group) ? $group->getDescription() : '',
                'groupLogo' => !empty($group->getLogo()) ? $group->getLogo()->getUrl() : '',
                'packName' => $notify[0]->getTitle(),
                'packCount' => $notify[0]->getCards()->filter(function (Card $c) {return !$c->getDeleted();})->count(),
                'link' => false,
            ]), 'text/html' );
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
            'category' => ['new-pack-notification']])));
        $this->send($message);

        return new Response();
    }

    /**
     * @param User $user
     * @param PartnerInvite $partner
     * @return Response
     */
    public function partnerInviteAction(User $user = null, PartnerInvite $partner = null)
    {
        /** @var $user User */
        if(empty($user))
            $user = $this->getUser();

        if($partner == null)
            $partner = $user->getPartnerInvites()->filter(function (PartnerInvite $p) {return $p->getActivated();})->first();

        if(empty($partner)) {
            $logger = $this->get('logger');
            $logger->error('Achievement called with no partner.');
            return new Response();
        }
        $codeUrl = $this->generateUrl('partner_welcome', ['_code' => $partner->getCode()], UrlGeneratorInterface::ABSOLUTE_URL);

        /** @var Swift_Mime_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject(($user->getFirst() ?: 'Your student') . ' needs your help with school.')
            ->setFrom($user->getEmail())
            ->setTo($partner->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:partner-invite.html.php', [
                        'user' => $user,
                        'greeting' => 'Hello ' . $partner->getFirst() . ' ' . $partner->getLast() . ',',
                        'link' => '<a href="' . $codeUrl . '" style="color: #FF9900;">If you are prepared to help ' . $user->getFirst() . ', click here to join Study Sauce and learn more about how we help students achieve their academic goals.</a>'
                    ]), 'text/html');
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
                        'category' => ['partner-invite']])));
        $this->send($message);

        return new Response();
    }

    /**
     * @param User $user
     * @param PartnerInvite $partner
     * @return Response
     */
    public function partnerReminderAction(User $user = null, PartnerInvite $partner = null)
    {
        /** @var $user User */
        if(empty($user))
            $user = $this->getUser();

        $codeUrl = $this->generateUrl('partner_welcome', ['_code' => $partner->getCode()], UrlGeneratorInterface::ABSOLUTE_URL);

        /** @var Swift_Mime_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject('Your invitation' . (!empty($user->getFirst()) ? (' from ' . $user->getFirst()) : '') . ' to join Study Sauce is still pending')
            ->setFrom($user->getEmail())
            ->setTo($partner->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:partner-reminder.html.php', [
                        'user' => $user,
                        'greeting' => 'Hello ' . $partner->getFirst() . ' ' . $partner->getLast() . ',',
                        'link' => '<a href="' . $codeUrl . '" style="color: #FF9900;">If you are prepared to help ' . $user->getFirst() . ', click here to join Study Sauce and learn more about how we help students achieve their academic goals.</a>'
                    ]), 'text/html');
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
                        'category' => ['partner-reminder']])));
        $this->send($message);

        return new Response();
    }

    /**
     * @param User $user
     * @param Invite $invite
     * @return Response
     */
    public function groupReminderAction(User $user = null, Invite $invite = null)
    {
        /** @var $user User */
        if(empty($user))
            $user = $this->getUser();

        /** @var Swift_Mime_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject('Your invitation to join Study Sauce is still pending')
            ->setFrom($user->getEmail())
            ->setTo(trim($invite->getEmail()))
            ->setBody($this->renderView('StudySauceBundle:Emails:group-invite.html.php', [
                'group' => !empty($invite->getGroup()) ? (' by ' . $invite->getGroup()->getDescription()) : '',
                'greeting' => 'Dear ' . $invite->getFirst() . ' ' . $invite->getLast() . ',',
                'link' => '<a href="' . $this->generateUrl('register', ['_code' => $invite->getCode()], UrlGeneratorInterface::ABSOLUTE_URL) . '" style="color: #FF9900;">Go to Study Sauce</a>'
            ]), 'text/html');
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
            'category' => ['group-reminder']])));
        $this->send($message);

        return new Response();
    }

    /**
     * @param User $user
     * @param StudentInvite $student
     * @return Response
     */
    public function studentInviteAction(User $user = null, StudentInvite $student = null)
    {
        /** @var $user User */
        if(empty($user))
            $user = $this->getUser();

        $codeUrl = $this->generateUrl('student_welcome', ['_code' => $student->getCode()], UrlGeneratorInterface::ABSOLUTE_URL);

        /** @var Swift_Mime_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject(($user->getFirst() ?: 'Your parent') . ' has asked for you to join Study Sauce')
            ->setFrom($user->getEmail())
            ->setTo($student->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:student-invite.html.php', [
                        'user' => $user,
                        'greeting' => 'Hello ' . $student->getFirst() . ' ' . $student->getLast() . ',',
                        'link' => '<a href="' . $codeUrl . '" style="color: #FF9900;">Go to Study Sauce</a>'
                    ]), 'text/html');
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
                        'category' => ['student-invite']])));
        $this->send($message);

        return new Response();
    }

    /**
     * @param User $user
     * @param Payment $payment
     * @param $address
     * @return Response
     */
    public function invoiceAction(User $user = null, Payment $payment, $address)
    {
        /** @var $user User */
        if(empty($user))
            $user = $this->getUser();

        $codeUrl = $this->generateUrl('home', [], UrlGeneratorInterface::ABSOLUTE_URL);

        /** @var Swift_Mime_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject('Thank you for your purchase!')
            ->setFrom('admin@studysauce.com')
            ->setTo($user->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:invoice.html.php', [
                        'user' => $user,
                        'address' => $address,
                        'payment' => $payment,
                        'greeting' => 'Hello ' . $user->getFirst() . ' ' . $user->getLast() . ',',
                        'link' => '<a href="' . $codeUrl . '" style="color: #FF9900;">Go to Study Sauce</a>'
                    ]), 'text/html');
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
                        'category' => ['invoice']])));
        $this->send($message);

        return new Response();
    }

    /**
     * @param $user
     * @return Response
     */
    public function marketingReminderAction(User $user)
    {
        /** @var $user User */
        if(empty($user))
            $user = $this->getUser();

        $codeUrl = $this->generateUrl('login', [], UrlGeneratorInterface::ABSOLUTE_URL);

        /** @var Swift_Mime_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject('Get the most out of your Study Sauce account')
            ->setFrom('admin@studysauce.com')
            ->setTo($user->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:welcome-reminder.html.php', [
                        'user' => $user,
                        'greeting' => 'Hello ' . $user->getFirst() . ' ' . $user->getLast() . ',',
                        'link' => '<a href="' . $codeUrl . '" style="color: #FF9900;">Go to Study Sauce</a>'
                    ]), 'text/html');
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
                        'category' => ['welcome-reminder']])));
        $this->send($message);

        return new Response();
    }

    /**
     * @param User $user
     * @param null $studentEmail
     * @param null $studentFirst
     * @param null $studentLast
     * @param $_code
     * @return Response
     */
    public function parentPrepayAction(User $user = null, $studentEmail = null, $studentFirst = null, $studentLast = null, $_code)
    {
        /** @var $user User */
        if(empty($user))
            $user = $this->getUser();

        $codeUrl = $this->generateUrl('student_welcome', ['_code' => $_code], UrlGeneratorInterface::ABSOLUTE_URL);

        /** @var Swift_Mime_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject(($user->getFirst() ?: 'Your parent') . ' has prepaid for your study plan')
            ->setFrom($user->getEmail())
            ->setTo($studentEmail)
            ->setBody($this->renderView('StudySauceBundle:Emails:prepay.html.php', [
                        'user' => $user,
                        'greeting' => 'Hello ' . $studentFirst . ' ' . $studentLast . ',',
                        'link' => '<a href="' . $codeUrl . '" style="color: #FF9900;">Go to Study Sauce</a>'
                    ]), 'text/html');
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
                        'category' => ['prepay']])));
        $this->send($message);

        return new Response();
    }

    /**
     * @param User $user
     * @param $reminders
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deadlineReminderAction($user, $reminders)
    {
        $reminderOutput = count($reminders) > 1 ? 'Below are your reminders.<br /><br />' : 'Below is your reminder.<br /><br />';
        $classes = [];
        if(is_array($reminders) && !empty($reminders)) {
            foreach ($reminders as $reminder) {
                /** @var Deadline $reminder */
                $color = !empty($reminder->getCourse()) ? $reminder->getCourse()->getColor() : '#DDDDDD';

                if($reminder->getAssignment() == 'Course completion' && ($reminder->getUser()->hasRole('ROLE_ADVISER')
                        ||  $reminder->getUser()->hasRole('ROLE_MASTER_ADVISER')))
                {
                    $reminderOutput .= '<br /><strong>Assignment:</strong><br /><img style="height:24px;width:24px;display:inline-block;vertical-align: middle;" src="https://studysauce.com/bundles/studysauce/images/course_icon.png" /> Complete the Study Sauce course<br /><br /><strong>Days until due date:</strong><br />' . $reminder->getDaysUntilDue() . '<br /><br />';
                    $classes[] = 'the Study Sauce course';
                }
                else {
                    $className = !empty($reminder->getCourse()) ? $reminder->getCourse()->getName() : 'Nonacademic';
                    $reminderOutput .= '<br /><strong>Subject:</strong><br /><span style="height:24px;width:24px;background-color:' . $color . ';display:inline-block;border-radius:100%;border: 3px solid #555555;vertical-align: middle;">&nbsp;</span> ' . $className . '<br /><br /><strong>Assignment:</strong><br />' . $reminder->getAssignment(
                        ) . '<br /><br /><strong>Days until due date:</strong><br />' . $reminder->getDaysUntilDue() . '<br /><br />';
                    if (array_search($className, $classes) === false) {
                        $classes[] = $className;
                    }
                }
            }
        }

        $codeUrl = $this->generateUrl('login', [], UrlGeneratorInterface::ABSOLUTE_URL);

        /** @var Swift_Mime_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject('You have a notification for ' . implode(', ', $classes))
            ->setFrom('admin@studysauce.com')
            ->setTo($user->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:deadline-reminder.html.php', [
                        'reminders' => $reminderOutput,
                        'greeting' => 'Hi ' . $user->getFirst() . ',',
                        'link' => '<a href="' . $codeUrl . '" style="color: #FF9900;">Click here to log in to Study Sauce and edit your deadlines</a>'
                    ]), 'text/html');
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
                        'category' => ['deadline-reminder']])));
        $this->send($message);

        return new Response();
    }

    /**
     * @param User $user
     * @param Deadline $deadline
     * @param $incomplete
     * @param $complete
     * @return Response
     */
    public function adviserCompletionAction(User $user, Deadline $deadline, $incomplete, $nosignup, $complete)
    {

        /** @var Swift_Mime_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject('Student completion ' . $deadline->getDaysUntilDue())
            ->setFrom('admin@studysauce.com')
            ->setTo($user->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:adviser-completion.html.php', [
                'incomplete' => $incomplete,
                'nosignup' => $nosignup,
                'complete' => $complete,
                'greeting' => 'Hi ' . $user->getFirst() . ',',
            ]), 'text/html');
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
            'category' => ['adviser-completion']])));
        $this->send($message);

        return new Response();
    }

    /**
     * @param User $user
     * @param PartnerInvite $partner
     * @return Response
     */
    public function achievementAction(User $user = null, PartnerInvite $partner = null)
    {
        /** @var $user User */
        if(empty($user))
            $user = $this->getUser();

        if($partner == null)
            $partner = $user->getPartnerInvites()->filter(function (PartnerInvite $p) {return $p->getActivated();})->first();

        if(empty($partner)) {
            $logger = $this->get('logger');
            $logger->error('Achievement called with no partner.');
            return new Response();
        }

        /** @var Swift_Mime_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject(($user->getFirst() ?: 'Your student') . ' has a study achievement and wanted you to know.')
            ->setFrom($user->getEmail())
            ->setTo($partner->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:achievement.html.php', [
                        'user' => $user,
                        'greeting' => 'Dear ' . $partner->getFirst() . ' ' . $partner->getLast() . ',',
                        'link' => '<a href="' . $this->generateUrl('goals', ['_code' => $partner->getCode()], UrlGeneratorInterface::ABSOLUTE_URL) . '" style="color: #FF9900;">Go to Study Sauce</a>'
                    ]), 'text/html');
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
                        'category' => ['achievement']])));
        $this->send($message);

        return new Response();
    }

    /**
     * @param User $user
     * @param ParentInvite $parent
     * @return Response
     */
    public function parentPayAction(User $user = null, ParentInvite $parent = null)
    {
        /** @var $user User */
        if(empty($user))
            $user = $this->getUser();

        $codeUrl = $this->generateUrl('parent_welcome', ['_code' => $parent->getCode()], UrlGeneratorInterface::ABSOLUTE_URL);

        /** @var Swift_Mime_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject(($user->getFirst() ?: 'Your student') . ' has asked for your help with school.')
            ->setFrom($user->getEmail())
            ->setTo($parent->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:parent-invite.html.php', [
                        'user' => $user,
                        'greeting' => 'Dear ' . $parent->getFirst() . ' ' . $parent->getLast() . ',',
                        'link' => '<a href="' . $codeUrl . '" style="color: #FF9900;">Go to Study Sauce</a>'
                    ]), 'text/html');
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
                        'category' => ['parent-invite']])));
        $this->send($message);

        return new Response();
    }

    /**
     * @param User $user
     * @param Invite $invite
     * @param Group $group
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function inviteAction(User $user = null, Invite $invite = null, Group $group = null)
    {
        /** @var $user User */
        if(empty($user))
            $user = $this->getUser();

        if(empty($group))
            $group = $invite->getGroup();

        $codeUrl = $this->generateUrl('register', ['_code' => $invite->getCode()], UrlGeneratorInterface::ABSOLUTE_URL);

        /** @var Swift_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject('Invitation to Study Sauce!')
            ->setFrom($invite->getUser()->getEmail())
            ->setTo($invite->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:group-invite.html.php', [
                        'group' => !empty($group) ? (' by ' . $group->getDescription()) : '',
                        'greeting' => 'Dear ' . $invite->getFirst() . ' ' . $invite->getLast() . ',',
                        'link' => '<a href="' . $codeUrl . '" style="color: #FF9900;">Go to Study Sauce</a>'
                    ]), 'text/html');
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
                        'category' => ['group-invite']])));
        $this->send($message);

        return new Response();
    }

    /**
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function resetPasswordAction(User $user = null)
    {
        /** @var $user User */
        if(empty($user))
            $user = $this->getUser();

        $codeUrl = $this->generateUrl('password_reset', ['token' => $user->getConfirmationToken()], UrlGeneratorInterface::ABSOLUTE_URL);

        /** @var Swift_Mime_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject('Your Study Sauce password has been reset.')
            ->setFrom('admin@studysauce.com')
            ->setTo($user->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:reset-password.html.php', [
                        'user' => $user,
                        'greeting' => 'Dear ' . $user->getFirst() . ' ' . $user->getLast() . ',',
                        'link' => '<a href="' . $codeUrl . '" style="color: #FF9900;">Create a new password</a>'
                    ]), 'text/html');
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
                        'category' => ['reset-password']])));
        $this->send($message);

        return new Response();
    }

    /**
     * @param User $user
     * @param $name
     * @param $email
     * @param $message
     * @return Response
     */
    public function contactMessageAction(User $user = null, $name, $email, $message)
    {
        if($user == null)
            $user = $this->getUser();

        /** @var \Swift_Mime_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject('Contact Us: From ' . $name)
            ->setFrom(!empty($user) ? $user->getEmail() : 'guest@studysauce.com')
            ->setTo('admin@studysauce.com')
            ->setBody($this->renderView('StudySauceBundle:Emails:contact-message.html.php', [
                        'link' => '&nbsp;',
                        'user' => $user,
                        'name' => $name,
                        'email' => $email,
                        'message' => str_replace(["\n"], ['<br />'], $message)
                    ]), 'text/html' );
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
                        'category' => ['contact-message']])));
        $this->sendToAdmin($message);

        return new Response();
    }

    public function sendInactivityDeadlines(User $user)
    {
        $link = $this->generateUrl('deadlines', [], UrlGeneratorInterface::ABSOLUTE_URL);

        /** @var \Swift_Mime_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject('Don\'t let deadlines sneak up on you')
            ->setFrom(!empty($user) ? $user->getEmail() : 'guest@studysauce.com')
            ->setTo($user->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:inactivity-deadlines.html.php', [
                'greeting' => 'Dear ' . $user->getFirst() . ',',
                'link' => '<a href="' . $link . '" class="cloak" style="color: #555555; text-decoration: none;">Click <span class="reveal" style="color: #FF9900;">here</span> to set up your Study Sauce deadline reminders</a>',
                'user' => $user
            ]), 'text/html' );
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
            'category' => ['inactivity-deadlines']])));
        $this->send($message);

        return new Response();
    }

    public function sendInactivityNotes(User $user)
    {
        $link = $this->generateUrl('notes', [], UrlGeneratorInterface::ABSOLUTE_URL);

        /** @var \Swift_Mime_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject('Never lose your study notes again')
            ->setFrom(!empty($user) ? $user->getEmail() : 'guest@studysauce.com')
            ->setTo($user->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:inactivity-notes.html.php', [
                'greeting' => 'Dear ' . $user->getFirst() . ',',
                'link' => '<a href="' . $link . '" class="cloak" style="color: #555555; text-decoration: none;">Click <span class="reveal" style="color: #FF9900;">here</span> to get organized</a>',
                'user' => $user
            ]), 'text/html' );
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
            'category' => ['inactivity-notes']])));
        $this->send($message);

        return new Response();
    }

    public function sendInactivityProcrastination(User $user)
    {
        $link = $this->generateUrl('course1_procrastination', [], UrlGeneratorInterface::ABSOLUTE_URL);

        /** @var \Swift_Mime_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject('Procrastinate much?')
            ->setFrom(!empty($user) ? $user->getEmail() : 'guest@studysauce.com')
            ->setTo($user->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:inactivity-procrastination.html.php', [
                'greeting' => 'Dear ' . $user->getFirst() . ',',
                'link' => '<a href="' . $link . '" class="cloak" style="color: #555555; text-decoration: none;">Click <span class="reveal" style="color: #FF9900;">here</span> to watch it.  Then again, maybe you will get around to it in a week or so...</a>',
                'user' => $user
            ]), 'text/html' );
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
            'category' => ['inactivity-procrastination']])));
        $this->send($message);

        return new Response();
    }

    public function sendInactivityCalculator(User $user)
    {
        $link = $this->generateUrl('calculator', [], UrlGeneratorInterface::ABSOLUTE_URL);

        /** @var \Swift_Mime_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject('What are your grades like this term?')
            ->setFrom(!empty($user) ? $user->getEmail() : 'guest@studysauce.com')
            ->setTo($user->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:inactivity-calculator.html.php', [
                'greeting' => 'Dear ' . $user->getFirst() . ',',
                'link' => '<a href="' . $link . '" class="cloak" style="color: #555555; text-decoration: none;">Use our grade calculator <span class="reveal" style="color: #FF9900;">here</span> to remove the guesswork and know where you stand with your grades.</a>',
                'user' => $user
            ]), 'text/html' );
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
            'category' => ['inactivity-calculator']])));
        $this->send($message);

        return new Response();
    }

    public function sendInactivityDistractions(User $user)
    {
        $link = $this->generateUrl('course1_distractions', [], UrlGeneratorInterface::ABSOLUTE_URL);

        /** @var \Swift_Mime_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject('Your cell phone is killing you...')
            ->setFrom(!empty($user) ? $user->getEmail() : 'guest@studysauce.com')
            ->setTo($user->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:inactivity-distractions.html.php', [
                'greeting' => 'Dear ' . $user->getFirst() . ',',
                'link' => '<a href="' . $link . '" class="cloak" style="color: #555555; text-decoration: none;">Click <span class="reveal" style="color: #FF9900;">here</span> to learn how to remove distractions from your school work.</a>',
                'user' => $user
            ]), 'text/html' );
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
            'category' => ['inactivity-distractions']])));
        $this->send($message);

        return new Response();
    }

    public function sendInactivityStudyTests(User $user)
    {
        $link = $this->generateUrl('course2_study_tests', [], UrlGeneratorInterface::ABSOLUTE_URL);

        /** @var \Swift_Mime_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject('Do you know how to study for tests?')
            ->setFrom(!empty($user) ? $user->getEmail() : 'guest@studysauce.com')
            ->setTo($user->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:inactivity-study-tests.html.php', [
                'greeting' => 'Dear ' . $user->getFirst() . ',',
                'link' => '<a href="' . $link . '" class="cloak" style="color: #555555; text-decoration: none;">Watch our studying for tests video <span class="reveal" style="color: #FF9900;">here</span>.</a>',
                'user' => $user
            ]), 'text/html' );
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
            'category' => ['inactivity-study-tests']])));
        $this->send($message);

        return new Response();
    }

    public function sendInactivityTestTaking(User $user)
    {
        $link = $this->generateUrl('course2_test_taking', [], UrlGeneratorInterface::ABSOLUTE_URL);

        /** @var \Swift_Mime_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject('Does taking tests freak you out?')
            ->setFrom(!empty($user) ? $user->getEmail() : 'guest@studysauce.com')
            ->setTo($user->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:inactivity-test-taking.html.php', [
                'greeting' => 'Dear ' . $user->getFirst() . ',',
                'link' => '<a href="' . $link . '" class="cloak" style="color: #555555; text-decoration: none;">Watch our test-taking video <span class="reveal" style="color: #FF9900;">here</span>.</a>',
                'user' => $user
            ]), 'text/html' );
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
            'category' => ['inactivity-test-taking']])));
        $this->send($message);

        return new Response();
    }

    public function sendInactivitySpacedRepetition(User $user)
    {
        $link = $this->generateUrl('course3_spaced_repetition', [], UrlGeneratorInterface::ABSOLUTE_URL);

        /** @var \Swift_Mime_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject('Why do you forget everything you study so quickly?')
            ->setFrom(!empty($user) ? $user->getEmail() : 'guest@studysauce.com')
            ->setTo($user->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:inactivity-spaced-repetition.html.php', [
                'greeting' => 'Dear ' . $user->getFirst() . ',',
                'link' => '<a href="' . $link . '" class="cloak" style="color: #555555; text-decoration: none;">Click <span class="reveal" style="color: #FF9900;">here</span> to watch our video on spaced repetition.</a>',
                'user' => $user
            ]), 'text/html' );
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
            'category' => ['inactivity-spaced-repetition']])));
        $this->send($message);

        return new Response();
    }

    public function sendInactivityStudyMetrics(User $user)
    {
        $link = $this->generateUrl('course2_study_metrics', [], UrlGeneratorInterface::ABSOLUTE_URL);

        /** @var \Swift_Mime_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject('Are you studying enough?')
            ->setFrom(!empty($user) ? $user->getEmail() : 'guest@studysauce.com')
            ->setTo($user->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:inactivity-study-metrics.html.php', [
                'greeting' => 'Dear ' . $user->getFirst() . ',',
                'link' => '<a href="' . $link . '" class="cloak" style="color: #555555; text-decoration: none;">Watch our study metrics video <span class="reveal" style="color: #FF9900;">here</span> to learn more.</a>',
                'user' => $user
            ]), 'text/html' );
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
            'category' => ['inactivity-study-metrics']])));
        $this->send($message);

        return new Response();
    }

    public function sendInactivityEnvironment(User $user)
    {
        $link = $this->generateUrl('course1_environment', [], UrlGeneratorInterface::ABSOLUTE_URL);

        /** @var \Swift_Mime_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject('Do you listen to music when you study?')
            ->setFrom(!empty($user) ? $user->getEmail() : 'guest@studysauce.com')
            ->setTo($user->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:inactivity-environment.html.php', [
                'greeting' => 'Dear ' . $user->getFirst() . ',',
                'link' => '<a href="' . $link . '" class="cloak" style="color: #555555; text-decoration: none;">Watch our video on study environments <span class="reveal" style="color: #FF9900;">here</span>.</a>',
                'user' => $user
            ]), 'text/html' );
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
            'category' => ['inactivity-environment']])));
        $this->send($message);

        return new Response();
    }

    public function sendInactivityPartner(User $user)
    {
        $link = $this->generateUrl('partner', [], UrlGeneratorInterface::ABSOLUTE_URL);

        /** @var \Swift_Mime_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject('Do you actually miss your parents holding you accountable?')
            ->setFrom(!empty($user) ? $user->getEmail() : 'guest@studysauce.com')
            ->setTo($user->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:inactivity-partner.html.php', [
                'greeting' => 'Dear ' . $user->getFirst() . ',',
                'link' => '<a href="' . $link . '" class="cloak" style="color: #555555; text-decoration: none;">Click <span class="reveal" style="color: #FF9900;">here</span> to set up your accountability partner.</a>',
                'user' => $user
            ]), 'text/html' );
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
            'category' => ['inactivity-partner']])));
        $this->send($message);

        return new Response();
    }

    public function sendInactivityActiveReading(User $user)
    {
        $link = $this->generateUrl('course3_active_reading', [], UrlGeneratorInterface::ABSOLUTE_URL);

        /** @var \Swift_Mime_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject('Stop spacing out when you read')
            ->setFrom(!empty($user) ? $user->getEmail() : 'guest@studysauce.com')
            ->setTo($user->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:inactivity-active-reading.html.php', [
                'greeting' => 'Dear ' . $user->getFirst() . ',',
                'link' => '<a href="' . $link . '" class="cloak" style="color: #555555; text-decoration: none;">Watch the active reading video <span class="reveal" style="color: #FF9900;">here</span> to improve your reading skills.</a>',
                'user' => $user
            ]), 'text/html' );
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
            'category' => ['inactivity-active-reading']])));
        $this->send($message);

        return new Response();
    }

    public function sendInactivityGoals(User $user)
    {
        $link = $this->generateUrl('goals', [], UrlGeneratorInterface::ABSOLUTE_URL);

        /** @var \Swift_Mime_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject('Did you know that simply setting goals makes you more likely to achieve them?')
            ->setFrom(!empty($user) ? $user->getEmail() : 'guest@studysauce.com')
            ->setTo($user->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:inactivity-goals.html.php', [
                'greeting' => 'Dear ' . $user->getFirst() . ',',
                'link' => '<a href="' . $link . '" class="cloak" style="color: #555555; text-decoration: none;">Set up your goals <span class="reveal" style="color: #FF9900;">here</span> and achieve them this term!</a>',
                'user' => $user
            ]), 'text/html' );
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
            'category' => ['inactivity-goals']])));
        $this->send($message);

        return new Response();
    }

    public function sendInactivityInterleaving(User $user)
    {
        $link = $this->generateUrl('course2_interleaving', [], UrlGeneratorInterface::ABSOLUTE_URL);

        /** @var \Swift_Mime_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject('Learn how to cross train your brain')
            ->setFrom(!empty($user) ? $user->getEmail() : 'guest@studysauce.com')
            ->setTo($user->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:inactivity-interleaving.html.php', [
                'greeting' => 'Dear ' . $user->getFirst() . ',',
                'link' => '<a href="' . $link . '" class="cloak" style="color: #555555; text-decoration: none;">This method is very effective, so take a few minutes and learn about it <span class="reveal" style="color: #FF9900;">here</span>.</a>',
                'user' => $user
            ]), 'text/html' );
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
            'category' => ['inactivity-interleaving']])));
        $this->send($message);

        return new Response();
    }

    public function sendInactivityGroupStudy(User $user)
    {
        $link = $this->generateUrl('course3_group_study', [], UrlGeneratorInterface::ABSOLUTE_URL);

        /** @var \Swift_Mime_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject('Make better use of your time studying with groups')
            ->setFrom(!empty($user) ? $user->getEmail() : 'guest@studysauce.com')
            ->setTo($user->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:inactivity-group-study.html.php', [
                'greeting' => 'Dear ' . $user->getFirst() . ',',
                'link' => '<a href="' . $link . '" class="cloak" style="color: #555555; text-decoration: none;">Watch the group study video <span class="reveal" style="color: #FF9900;">here</span> to transform your group and make the most of your time together.</a>',
                'user' => $user
            ]), 'text/html' );
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
            'category' => ['inactivity-group-study']])));
        $this->send($message);

        return new Response();
    }

    public function sendInactivityTeaching(User $user)
    {
        $link = $this->generateUrl('course3_teaching', [], UrlGeneratorInterface::ABSOLUTE_URL);

        /** @var \Swift_Mime_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject('Memorizing facts won\'t help you...')
            ->setFrom(!empty($user) ? $user->getEmail() : 'guest@studysauce.com')
            ->setTo($user->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:inactivity-teaching.html.php', [
                'greeting' => 'Dear ' . $user->getFirst() . ',',
                'link' => '<a href="' . $link . '" class="cloak" style="color: #555555; text-decoration: none;">Watch our video <span class="reveal" style="color: #FF9900;">here</span> to learn how to study for classes with more complicated subject matters.</a>',
                'user' => $user
            ]), 'text/html' );
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
            'category' => ['inactivity-teaching']])));
        $this->send($message);

        return new Response();
    }

    /**
     * @param User $user
     * @param $properties
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function administratorAction(User $user = null, $properties)
    {
        if($user == null)
            $user = $this->getUser();

        /** @var $orm EntityManager */
        if(is_object($properties)) {
            if($properties instanceof HttpExceptionInterface) {
                $subject = 'HTTP Error: ' . $properties->getStatusCode();
            }
            elseif ($properties instanceof \Exception) {
                $subject = 'Error: ' . get_class($properties);
            }
            else {
                $subject = 'Message Type: ' . get_class($properties);
            }
        }
        elseif($this->get('request')->get('_controller') == 'StudySauceBundle:Plan:widget') {
            $subject = 'Study plan overlap: ' . $properties['student'];
        }
        else {
            $subject = 'Message from ' . $this->get('request')->get('_controller');
        }

        /** @var \Swift_Mime_SimpleMessage $message */
        $message = Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom(!empty($user) ? $user->getEmail() : 'guest@studysauce.com')
            ->setTo('admin@studysauce.com')
            ->setBody($this->renderView('StudySauceBundle:Emails:administrator.html.php', [
                        'link' => '&nbsp;',
                        'user' => $user,
                        'properties' => self::dump($properties, $properties instanceof \Exception ? 3 : 2)
                    ]), 'text/html' );
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
                        'category' => ['administrator']])));
        $this->sendToAdmin($message);

        return new Response();
    }

    /**
     * @param Swift_Mime_Message $message
     */
    protected function send(\Swift_Mime_Message $message)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        $to = $message->getTo();
        reset($to);

        if($this->container->getParameter('defer_all_emails') !== false) {
            $message->getHeaders()->addParameterizedHeader('X-Original-To', key($to));
            $message->setTo(explode(';', $this->container->getParameter('defer_all_emails') ?: 'brian@studysauce.com'));
        }

        // check to make sure the limit hasn't been reached
        $count = $orm->getRepository('StudySauceBundle:Mail')->createQueryBuilder('m')
            ->select('COUNT(DISTINCT m.id)')
            ->andWhere('m.message LIKE \'%s:' . (strlen(key($to))) . ':"' . key($to) . '"%\'')
            ->andWhere('m.message LIKE \'%s:' . strlen($message->getHeaders()->get('X-SMTPAPI')->getFieldBody()) . ':"' . $message->getHeaders()->get('X-SMTPAPI')->getFieldBody() . '"%\'')
            ->andWhere('m.created > :today')
            ->setParameter('today', new \DateTime('today'))
            ->getQuery()
            ->getSingleScalarResult();

        if($count >= 2)
        {
            $message->setSubject('CANCELLED: ' . $message->getSubject());
            $message->getHeaders()->addParameterizedHeader('X-Original-To', key($to));
            $message->setTo(explode(';', $this->container->getParameter('defer_all_emails') ?: 'brian@studysauce.com'));
        }

        /** @var \Swift_Mailer $mailer */
        $mailer = $this->get('mailer');
        $mailer->send($message);
    }

    /**
     * @param Swift_Mime_Message $message
     */
    protected function sendToAdmin(\Swift_Mime_Message $message)
    {
        if($this->container->getParameter('defer_all_emails') !== false) {
            $message->setTo(explode(';', $this->container->getParameter('defer_all_emails') ?: 'brian@studysauce.com'));
        }
        /** @var \Swift_Transport_EsmtpTransport $transport */
        $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, 'ssl')
            ->setUsername('brian@studysauce.com')
            ->setPassword('Da1ddy23');
        /** @var \Swift_Mailer $mailer */
        $mailer = \Swift_Mailer::newInstance($transport);
        $mailer->send($message);
    }

    private static $_objects;
    private static $_output;
    private static $_depth;

    /**
     * Converts a variable into a string representation.
     * This method achieves the similar functionality as var_dump and print_r
     * but is more robust when handling complex objects such as PRADO controls.
     * @param mixed $var variable to be dumped
     * @param integer $depth maximum depth that the dumper should go into the variable. Defaults to 10.
     * @param bool $highlight
     * @return string the string representation of the variable
     */
    public static function dump($var,$depth=10,$highlight=false)
    {
        self::$_output='';
        self::$_objects=[];
        self::$_depth=$depth;
        self::dumpInternal($var,0);
        if($highlight)
        {
            $result=highlight_string("<?php\n".self::$_output,true);
            return preg_replace('/&lt;\\?php<br \\/>/','',$result,1);
        }
        else
            return self::$_output;
    }

    /**
     * @param $var
     * @param $level
     */
    private static function dumpInternal($var,$level)
    {
        switch(gettype($var))
        {
            case 'boolean':
                self::$_output.=$var?'true':'false';
                break;
            case 'integer':
                self::$_output.="$var";
                break;
            case 'double':
                self::$_output.="$var";
                break;
            case 'string':
                self::$_output.="'$var'";
                break;
            case 'resource':
                self::$_output.='{resource}';
                break;
            case 'NULL':
                self::$_output.="null";
                break;
            case 'unknown type':
                self::$_output.='{unknown}';
                break;
            case 'array':
                if(self::$_depth<=$level)
                    self::$_output.='array(...)';
                else if(empty($var))
                    self::$_output.='array()';
                else
                {
                    $keys=array_keys($var);
                    $spaces=str_repeat(' ',$level*4);
                    self::$_output.="array\n".$spaces.'(';
                    foreach($keys as $key)
                    {
                        self::$_output.="\n".$spaces."    [$key] => ";
                        self::dumpInternal($var[$key],$level+1);
                    }
                    self::$_output.="\n".$spaces.')';
                }
                break;
            case 'object':
                if(($id=array_search($var,self::$_objects,true))!==false)
                    self::$_output.=get_class($var).'#'.($id+1).'(...)';
                else if(self::$_depth<=$level)
                    self::$_output.=get_class($var).'(...)';
                else
                {
                    $id=array_push(self::$_objects,$var);
                    $className=get_class($var);
                    $members=(array)$var;
                    $keys=array_keys($members);
                    $spaces=str_repeat(' ',$level*4);
                    self::$_output.="$className#$id\n".$spaces.'(';
                    foreach($keys as $key)
                    {
                        $keyDisplay=strtr(trim($key),["\0"=>':']);
                        self::$_output.="\n".$spaces."    [$keyDisplay] => ";
                        self::dumpInternal($members[$key],$level+1);
                    }
                    self::$_output.="\n".$spaces.')';
                }
                break;
        }
    }


}