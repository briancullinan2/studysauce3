<?php

namespace StudySauce\Bundle\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use EDAM\Error\EDAMSystemException;
use EDAM\Types\Notebook;
use EDAM\Types\Tag;
use Evernote\Client as EvernoteClient;
use StudySauce\Bundle\Controller\EmailsController;
use StudySauce\Bundle\Controller\NotesController;
use StudySauce\Bundle\Controller\PlanController;
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Deadline;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\GroupInvite;
use StudySauce\Bundle\Entity\PartnerInvite;
use StudySauce\Bundle\Entity\Schedule;
use StudySauce\Bundle\Entity\StudyNote;
use StudySauce\Bundle\Entity\User;
use Swift_Mailer;
use Swift_Transport;
use Swift_Transport_SpoolTransport;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use WhiteOctober\SwiftMailerDBBundle\Spool\DatabaseSpool;

/**
 * Hello World command for demo purposes.
 *
 * You could also extend from Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
 * to get access to the container via $this->getContainer().
 *
 * @author Tobias Schultze <http://tobion.de>
 */
class CronSauceCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        // use sauce:cron to send reminder emails
        $this
            ->setName('sauce:cron')
            ->setDescription('Run all the periodic things Study Sauce needs to do.')
            //->addArgument('who', InputArgument::OPTIONAL, 'Who to greet.', 'World')
            ->setHelp(
                <<<EOF
                The <info>%command.name%</info> command performs the following tasks:
* Send reminder e-mails
* Clear the mail queue
* Syncs notes and events with services
<info>php %command.full_name%</info>

EOF
            )
            ->addOption(
                'emails',
                null,
                InputOption::VALUE_NONE,
                'If set, cron will only run emails'
            )
            ->addOption(
                'sync',
                null,
                InputOption::VALUE_NONE,
                'If set, cron will only sync'
            );;
    }

    private function sendReminders()
    {

        /** @var $orm EntityManager */
        $orm = $this->getContainer()->get('doctrine')->getManager();
        $emails = new EmailsController();
        $emails->setContainer($this->getContainer());

        // send reminders
        $partners = $orm->getRepository('StudySauceBundle:PartnerInvite')->createQueryBuilder('p')
            ->where('p.activated=0 AND p.partner IS NULL')
            ->andWhere('p.reminder IS NULL OR p.reminder < :reminder')
            ->andWhere('(p.created < :d1 AND p.created > :d2) OR (p.created < :d3 AND p.created > :d4)' .
                ' OR (p.created < :d5 AND p.created > :d6) OR (p.created < :d7 AND p.created > :d8)')
            ->setParameter('reminder', date_sub(new \DateTime(), new \DateInterval('P7D')))
            ->setParameter('d1', date_sub(new \DateTime(), new \DateInterval('P3D')))
            ->setParameter('d2', date_sub(new \DateTime(), new \DateInterval('P4D')))
            ->setParameter('d3', date_sub(new \DateTime(), new \DateInterval('P10D')))
            ->setParameter('d4', date_sub(new \DateTime(), new \DateInterval('P11D')))
            ->setParameter('d5', date_sub(new \DateTime(), new \DateInterval('P17D')))
            ->setParameter('d6', date_sub(new \DateTime(), new \DateInterval('P18D')))
            ->setParameter('d7', date_sub(new \DateTime(), new \DateInterval('P24D')))
            ->setParameter('d8', date_sub(new \DateTime(), new \DateInterval('P25D')))
            ->getQuery()->getResult();
        foreach ($partners as $i => $p) {
            try {
                /** @var PartnerInvite $p */
                // send for 4 weeks
                $emails->partnerReminderAction($p->getUser(), $p);
                $p->setReminder(new \DateTime());
                $orm->merge($p);
                $orm->flush();
            } catch (\Exception $e) {
                $error = $e;
            }
        }

        $groupInvites = $orm->getRepository('StudySauceBundle:GroupInvite')->createQueryBuilder('g')
            ->where('g.activated=0 AND g.student IS NULL')
            ->andWhere('g.reminder IS NULL OR g.reminder < :reminder')
            ->andWhere('(g.created < :d1 AND g.created > :d2) OR (g.created < :d3 AND g.created > :d4)' .
                ' OR (g.created < :d5 AND g.created > :d6) OR (g.created < :d7 AND g.created > :d8)')
            ->setParameter('reminder', date_sub(new \DateTime(), new \DateInterval('P7D')))
            ->setParameter('d1', date_sub(new \DateTime(), new \DateInterval('P3D')))
            ->setParameter('d2', date_sub(new \DateTime(), new \DateInterval('P4D')))
            ->setParameter('d3', date_sub(new \DateTime(), new \DateInterval('P10D')))
            ->setParameter('d4', date_sub(new \DateTime(), new \DateInterval('P11D')))
            ->setParameter('d5', date_sub(new \DateTime(), new \DateInterval('P17D')))
            ->setParameter('d6', date_sub(new \DateTime(), new \DateInterval('P18D')))
            ->setParameter('d7', date_sub(new \DateTime(), new \DateInterval('P24D')))
            ->setParameter('d8', date_sub(new \DateTime(), new \DateInterval('P25D')))
            ->getQuery()->getResult();
        foreach ($groupInvites as $i => $g) {
            try {
                /** @var GroupInvite $g */
                // send for 4 weeks
                $emails->groupReminderAction($g->getUser(), $g);
                $g->setReminder(new \DateTime());
                $orm->merge($g);
                $orm->flush();
            } catch (\Exception $e) {
                $error = $e;
            }
        }

        if(!empty($error))
            throw $error;
    }

    private function send3DayMarketing()
    {
        /** @var $orm EntityManager */
        $orm = $this->getContainer()->get('doctrine')->getManager();

        // send 3 day signup reminder
        $users = $orm->getRepository('StudySauceBundle:User');
        /** @var QueryBuilder $qb */
        $qb = $users->createQueryBuilder('u')
            ->andWhere('u.created > \'' . date_timestamp_set(new \DateTime(), time() - 86400 * 4)->format('Y-m-d 00:00:00') . '\'')
            ->andWhere('u.properties NOT LIKE \'%s:16:"welcome_reminder";%\' OR u.properties IS NULL')
            ->andWhere('u.roles NOT LIKE \'%GUEST%\' AND u.roles NOT LIKE \'%DEMO%\'');
        $users = $qb->getQuery()->execute();
        foreach ($users as $i => $u) {
            /** @var User $u */
            // TODO: skip advised users
            if ($u->getCreated()->getTimestamp() < time() - 86400 * 3 && $u->getCreated()->getTimestamp() > time() - 86400 * 4) {
                $u->setProperty('welcome_reminder', time());
                //$emails->marketingReminderAction($u);
                /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
                $userManager = $this->getContainer()->get('fos_user.user_manager');
                $userManager->updateUser($u);
            }
        }

    }

    private function sendDeadlines()
    {
        /** @var $orm EntityManager */
        $orm = $this->getContainer()->get('doctrine')->getManager();
        $emails = new EmailsController();
        $emails->setContainer($this->getContainer());

        // send deadline reminders
        $reminders = new ArrayCollection($orm->getRepository('StudySauceBundle:Deadline')->createQueryBuilder('d')
            ->select('d')
            ->leftJoin('d.user', 'u')
            ->andWhere('d.user IS NOT NULL AND d.deleted != 1')
            ->andWhere('d.dueDate >= :now OR u.roles LIKE \'%adviser%\'')
            ->setParameter('now', new \DateTime('today'))
            ->getQuery()
            ->getResult());
        $deadlines = [];

        // create a list of adviser deadlines
        $reminderRecipients = [];
        $adviser = $reminders->filter(function (Deadline $d) {
            return $d->getUser()->hasRole('ROLE_ADVISER') || $d->getUser()->hasRole('ROLE_MASTER_ADVISER');});
        foreach ($adviser->toArray() as $i => $d) {
            /** @var Deadline $d */

            /** @var Deadline $adviserCompletion */
            $adviserCompletion = $adviser->filter(function (Deadline $r) use ($d) {return $r->getAssignment() == 'Adviser completion' && $d->getUser() == $r->getUser();})->first();

            if($d->shouldSend() && $d->getAssignment() == 'Course completion')
            {
                // get a list of all users in the group
                $addresses = [];
                $incomplete = [];
                $complete = [];
                foreach($d->getUser()->getGroups()->toArray() as $g)
                {
                    /** @var Group $g */
                    foreach($g->getUsers()->toArray() as $u)
                    {
                        /** @var User $u */
                        $addresses[] = $u->getEmail();

                        if($u->hasRole('ROLE_ADVISER') || $u->hasRole('ROLE_MASTER_ADVISER') ||
                            $u->hasRole('ROLE_DEMO') || $u->hasRole('ROLE_ADMIN') ||
                            $u->hasRole('ROLE_PARTNER') || $u->hasRole('ROLE_PARENT'))
                            continue;

                        if($u->getCompleted() < 100) {
                            $incomplete[$u->getId()] = $u;
                            $deadlines[$u->getId()][] = $d;
                            $reminderRecipients[$u->getId()] = $u;
                        }
                        else {
                            $complete[$u->getId()] = $u;
                        }
                    }
                }

                // also send reminder to users that haven't even signed up
                $nosignup = [];
                foreach($d->getUser()->getGroupInvites() as $gi)
                {
                    /** @var GroupInvite $gi */
                    if(array_search($gi->getEmail(), $addresses) === false)
                    {
                        $r = md5($gi->getEmail());
                        $reminderRecipients[$r] = $gi;
                        $deadlines[$r][] = $d;
                        $nosignup[] = $gi;
                    }
                }

                // send adviser updates
                usort($incomplete, function (User $a, User $b) {
                    return strcmp($a->getLast(), $b->getLast());
                });
                usort($nosignup, function (GroupInvite $a, GroupInvite $b) {
                    return strcmp($a->getLast(), $b->getLast());
                });
                usort($complete, function (User $a, User $b) {
                    return strcmp($a->getLast(), $b->getLast());
                });
                if(!empty($adviserCompletion) && $adviserCompletion->shouldSend() &&
                    !empty($incomplete)) {
                    $emails->adviserCompletionAction($d->getUser(), $d, $incomplete, $nosignup, $complete);
                    $adviserCompletion->markSent();
                    $orm->merge($adviserCompletion);
                }

                $d->markSent();
                $orm->merge($d);
                $orm->flush();
            }
        }

        // user deadlines
        foreach ($reminders as $i => $d) {
            /** @var Deadline $d */
            // don't send advisers their own reminders, only send them to students above
            if($d->getUser()->hasRole('ROLE_ADVISER') || $d->getUser()->hasRole('ROLE_MASTER_ADVISER') ||
                $d->getUser()->hasRole('ROLE_DEMO') || $d->getUser()->hasRole('ROLE_ADMIN') ||
                $d->getUser()->hasRole('ROLE_GUEST'))
                continue;
            // due tomorrow
            if ($d->shouldSend()) {
                $deadlines[$d->getUser()->getId()][] = $d;
                $reminderRecipients[$d->getUser()->getId()] = $d->getUser();
                $d->markSent();
                $orm->merge($d);
                $orm->flush();
            }
        }

        // send aggregate emails
        foreach ($deadlines as $i => $all) {
            $user = $reminderRecipients[$i];
            $emails->deadlineReminderAction($user, $all);
        }

    }

    private function sendInactivity()
    {
        // send inactivity email
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->getContainer()->get('fos_user.user_manager');
        /** @var $orm EntityManager */
        $orm = $this->getContainer()->get('doctrine')->getManager();
        $emails = new EmailsController();
        $emails->setContainer($this->getContainer());

        // send 3 day signup reminder
        $orm->flush();
        $users = $orm->getRepository('StudySauceBundle:User')->createQueryBuilder('u')
            ->andWhere('u.lastLogin <= \'' . date_sub(new \DateTime(), new \DateInterval('P7D'))->format('Y-m-d') . ' 00:00:00\'')
            ->andWhere('u.created >= \'2015-07-27 00:00:00\'')
            ->getQuery()->execute();
        foreach ($users as $i => $u) {
            /** @var User $u */
            if($u->hasRole('ROLE_ADVISER') || $u->hasRole('ROLE_MASTER_ADVISER') || $u->hasRole('ROLE_PARENT') || $u->hasRole('ROLE_PARTNER') ||
                $u->hasRole('ROLE_ADMIN') || $u->hasRole('ROLE_GUEST') || $u->hasRole('ROLE_DEMO'))
                continue;

            if(empty($u->getProperty('inactivity')) || !is_numeric($u->getProperty('inactivity')) ||
                $u->getProperty('inactivity') < time() - 86400 * 7) {
                $u->setProperty('inactivity', time());

                // send deadline reminder
                if($u->getDeadlines()->count() == 0 && empty($u->getProperty('seen_deadlines'))) {
                    $u->setProperty('seen_deadlines', time());
                    $emails->sendInactivityDeadlines($u);
                }
                elseif($u->getNotes()->count() == 0 && empty($u->getProperty('seen_notes'))) {
                    $u->setProperty('seen_notes', time());
                    $emails->sendInactivityNotes($u);
                }
                elseif(($u->getCourse1s()->count() == 0 || $u->getCourse1s()->first()->getLesson4() < 4) &&
                    empty($u->getProperty('seen_procrastination'))) {
                    $u->setProperty('seen_procrastination', time());
                    $emails->sendInactivityProcrastination($u);
                }
                /** @var Schedule $schedule */
                elseif((empty($schedule = $u->getSchedules()->first()) ||
                        !$schedule->getCourses()->exists(function ($_, Course $c) {
                            return $c->getGrades()->count() > 0;})) &&
                    empty($u->getProperty('seen_calculator'))) {
                    $u->setProperty('seen_calculator', time());
                    $emails->sendInactivityCalculator($u);
                }
                elseif(($u->getCourse1s()->count() == 0 || $u->getCourse1s()->first()->getLesson3() < 4) &&
                    empty($u->getProperty('seen_distractions'))) {
                    $u->setProperty('seen_distractions', time());
                    $emails->sendInactivityDistractions($u);
                }
                elseif(($u->getCourse2s()->count() == 0 || $u->getCourse2s()->first()->getLesson4() < 4) &&
                    empty($u->getProperty('seen_study_tests'))) {
                    $u->setProperty('seen_study_tests', time());
                    $emails->sendInactivityStudyTests($u);
                }
                elseif(($u->getCourse2s()->count() == 0 || $u->getCourse2s()->first()->getLesson5() < 4) &&
                    empty($u->getProperty('seen_test_taking'))) {
                    $u->setProperty('seen_test_taking', time());
                    $emails->sendInactivityTestTaking($u);
                }
                elseif(($u->getCourse3s()->count() == 0 || $u->getCourse3s()->first()->getLesson5() < 4) &&
                    empty($u->getProperty('seen_spaced_repetition'))) {
                    $u->setProperty('seen_spaced_repetition', time());
                    $emails->sendInactivitySpacedRepetition($u);
                }
                elseif(($u->getCourse2s()->count() == 0 || $u->getCourse2s()->first()->getLesson1() < 4) &&
                    empty($u->getProperty('seen_study_metrics'))) {
                    $u->setProperty('seen_study_metrics', time());
                    $emails->sendInactivityStudyMetrics($u);
                }
                elseif(($u->getCourse1s()->count() == 0 || $u->getCourse1s()->first()->getLesson5() < 4) &&
                    empty($u->getProperty('seen_environment'))) {
                    $u->setProperty('seen_environment', time());
                    $emails->sendInactivityEnvironment($u);
                }
                elseif(empty($u->getPartnerOrAdviser()) &&
                    empty($u->getProperty('seen_partner'))) {
                    $u->setProperty('seen_partner', time());
                    $emails->sendInactivityPartner($u);
                }
                elseif(($u->getCourse3s()->count() == 0 || $u->getCourse3s()->first()->getLesson4() < 4) &&
                    empty($u->getProperty('seen_active_reading'))) {
                    $u->setProperty('seen_active_reading', time());
                    $emails->sendInactivityActiveReading($u);
                }
                elseif($u->getGoals()->count() == 0 && empty($u->getProperty('seen_goals'))) {
                    $u->setProperty('seen_goals', time());
                    $emails->sendInactivityGoals($u);
                }
                elseif(($u->getCourse2s()->count() == 0 || $u->getCourse2s()->first()->getLesson3() < 4) &&
                    empty($u->getProperty('seen_interleaving'))) {
                    $u->setProperty('seen_interleaving', time());
                    $emails->sendInactivityInterleaving($u);
                }
                elseif(($u->getCourse3s()->count() == 0 || $u->getCourse3s()->first()->getLesson2() < 4) &&
                    empty($u->getProperty('seen_group_study'))) {
                    $u->setProperty('seen_group_study', time());
                    $emails->sendInactivityGroupStudy($u);
                }
                elseif(($u->getCourse3s()->count() == 0 || $u->getCourse3s()->first()->getLesson3() < 4) &&
                    empty($u->getProperty('seen_teaching'))) {
                    $u->setProperty('seen_teaching', time());
                    $emails->sendInactivityTeaching($u);
                }

                $userManager->updateUser($u);
            }
        }
    }

    private function sendSpool()
    {
        // clear mail spool
        /** @var Swift_Mailer $mailer */
        $mailer = $this->getContainer()->get('mailer');
        /** @var Swift_Transport_SpoolTransport $transport */
        $transport = $mailer->getTransport();
        /** @var DatabaseSpool $spool */
        $spool = $transport->getSpool();
        $spool->setTimeLimit(60*4.5);
        /** @var Swift_Transport $queue */
        $queue = $this->getContainer()->get('swiftmailer.transport.real');
        $spool->flushQueue($queue);

    }

    private function syncNotes()
    {

        /** @var $orm EntityManager */
        $orm = $this->getContainer()->get('doctrine')->getManager();

        // sync user notes
        // list all users with an evernote access token
        $users = $orm->getRepository('StudySauceBundle:User');
        /** @var QueryBuilder $qb */
        $qb = $users->createQueryBuilder('u')
            ->andWhere('u.evernote_access_token IS NOT NULL AND u.evernote_access_token != \'\'');
        $users = $qb->getQuery()->execute();
        foreach($users as $u) {
            /** @var User $u */
            try {
                NotesController::syncNotes($u, $this->getContainer());
            }
            catch (\Exception $e) {
                if($e instanceof \EDAM\Error\EDAMSystemException) {
                    if($e->errorCode == 8) {
                        $u->setEvernoteAccessToken(null);
                        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
                        $userManager = $this->getContainer()->get('fos_user.user_manager');
                        $userManager->updateUser($u);
                    }
                    elseif($e->errorCode == 19) {
                        // ignore rate limit errors
                    }
                    else
                        $error = $e;
                }
                else
                    $error = $e;
            }
        }
        if(!empty($error))
            throw $error;
    }

    private function syncEvents()
    {
        /** @var $orm EntityManager */
        $orm = $this->getContainer()->get('doctrine')->getManager();


        // sync calendar
        $users = $orm->getRepository('StudySauceBundle:User');
        /** @var QueryBuilder $qb */
        $qb = $users->createQueryBuilder('u')
            ->andWhere('u.gcal_access_token IS NOT NULL AND u.gcal_access_token != \'\'');
        $users = $qb->getQuery()->execute();
        foreach($users as $u) {
            try {
                PlanController::syncEvents($u, $this->getContainer());
            }
            catch (\Exception $e) {
                if(strpos($e->getMessage(), 'Rate Limit Exceeded') !== false) {
                    // ignore rate limit errors
                }
                else
                    $error = $e;
            }
        }
        if(!empty($error))
            throw $error;

    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // set the timeout to 4 and a half minutes
        set_time_limit(60*6);
        if(!$input->getOption('sync')) {
            try {
                $this->sendReminders();
                //$this->send3DayMarketing();
            }
            catch (\Exception $e) {
                $error = $e;
            }
            try {
                $this->sendDeadlines();
            }
            catch (\Exception $e) {
                $error = $e;
            }
            try {
                $this->sendInactivity();
            }
            catch (\Exception $e) {
                $error = $e;
            }
            try {
                $this->sendSpool();
            }
            catch (\Exception $e) {
                $error = $e;
            }
        }
        if(!$input->getOption('emails')) {
            try {
                $this->syncNotes();
            }
            catch (\Exception $e) {
                $error = $e;
            }
            try {
                $this->syncEvents();
            }
            catch (\Exception $e) {
                $error = $e;
            }
        }
        if(!empty($error))
            throw $error;
    }
}
