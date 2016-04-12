<?php

namespace StudySauce\Bundle\Command;

use Doctrine\ORM\EntityManager;
use StudySauce\Bundle\Controller\EmailsController;
use StudySauce\Bundle\Controller\PacksController;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Invite;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\Response;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\Entity\UserPack;
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
            )
            ->addOption(
                'notify',
                null,
                InputOption::VALUE_NONE,
                'If set, cron will only notify pack changes'
            );;
    }

    private function sendReminders()
    {

        /** @var $orm EntityManager */
        $orm = $this->getContainer()->get('doctrine')->getManager();
        $emails = new EmailsController();
        $emails->setContainer($this->getContainer());

        // send reminders

        $groupInvites = $orm->getRepository('StudySauceBundle:Invite')->createQueryBuilder('g')
            ->where('g.activated=0 AND g.invitee IS NULL')
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
                /** @var Invite $g */
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

    public function sendNotifications() {
        /** @var $orm EntityManager */
        $orm = $this->getContainer()->get('doctrine')->getManager();

        $users = $orm->getRepository('StudySauceBundle:User')->createQueryBuilder('u')
            ->getQuery()->getResult();

        $controller = new PacksController();
        $controller->setContainer($this->getContainer());
        $emails = new EmailsController();
        $emails->setContainer($this->getContainer());

        foreach($users as $u) {
            /** @var User $u */
            $packs = $controller->getPacksForUser($u);

            $notify = [];
            // loop through packs and determine if they have already been downloaded by the user
            foreach($packs as $p) {

                $children = $controller->getChildUsersForPack($p, $u);

                // same conditions as on PackControllers listAction()
                if($p->getStatus() == 'DELETED' || $p->getStatus() == 'UNPUBLISHED' || empty($p->getStatus())
                    // skip packs that aren't associated with an account
                    || count($children) == 0 || empty($p->getProperty('schedule'))
                    // skip packs that haven't release yet
                    || $p->getProperty('schedule') > new \DateTime()) {
                    continue;
                }

                /** @var Pack $p */
                foreach($children as $c) {
                    /** @var User $c */
                    if ($p->getUserPacks()->filter(function (UserPack $up) use ($c) {
                            return $up->getUser() == $c && !empty($up->getDownloaded()) && !$up->getRemoved();})->count() == 0
                        || $c->getResponses()->filter(function (Response $r) use ($p) {
                                return $r->getCard()->getPack() == $p && $r->getCreated() <= new \DateTime();
                            })->count() == 0) {
                        $notify[] = [$p, $c];
                    }
                }
            }

            /** @var Pack[] $difference */
            $difference = [];
            foreach($notify as list($p, $c)) {
                if (!in_array($p->getId(), $u->getProperty('notified') ?: [])) {
                    $difference[] = $p;
                }
            }

            if (count($difference) > 0) {

                $u->setProperty('notified', array_unique(array_merge(array_map(function ($n) {
                    /** @var Pack $p */
                    list($p) = $n;
                    return $p->getId(); }, $notify), $u->getProperty('notified') ?: [])));
                $this->getContainer()->get('fos_user.user_manager')->updateUser($u);

                /** @var Invite $groupInvite */
                $groupInvite = $u->getInvites()->filter(function (Invite $i) {
                    return !empty($i->getInvitee()) && $i->getInvitee()->getGroups()->count() > 0;})->first();

                /** @var Group $group */
                if (!empty($groupInvite)) {
                    $group = $groupInvite->getInvitee()->getGroups()->first();
                }

                /** @var Pack[] $alerting */
                $alerting = array_values(array_filter($difference, function (Pack $p) {
                    return $p->getProperty('alert') == true;
                }));

                // TODO: uncomment this when notifications are working
                if (count($alerting) > 0) {
                    foreach($u->getDevices() as $d) {
                        if (!empty($group)) {
                            //$controller->sendNotification($group->getName() . ' added a new pack, "'
                            //    . $alerting[0]->getTitle() . '"!', count($notify), str_replace([' ', '<', '>'], '', $d));
                        }
                        else {
                            //$controller->sendNotification('You have a new pack "' . $alerting[0]->getTitle()
                            //    . '" on Study Sauce!', count($notify), str_replace([' ', '<', '>'], '', $d));
                        }
                    }
                }

                /** @var Pack[] $emailing */
                $emailing = array_values(array_filter($difference, function (Pack $p) {
                    return $p->getProperty('email') == true;
                }));

                $child = array_values(array_filter($notify, function ($n) use ($u) { return $n[1] != $u; }));

                if(count($emailing) > 0) {
                    //$emails->sendNewPacksNotification($u, $emailing, !empty($child) ? $child[1]->getFirst() : '');
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // set the timeout to 4 and a half minutes
        $empty = !$input->getOptions()['emails'] && !$input->getOptions()['sync'] && !$input->getOptions()['notify'];
        set_time_limit(60*6);
        $options = $input->getOptions();
        if($empty || !empty($input->getOptions()['sync'])) {

        }
        if($empty || !empty($input->getOptions()['notify'])) {
            $this->sendNotifications();
        }
        if($empty || !empty($input->getOptions()['emails'])) {
            try {
                $this->sendReminders();
                //$this->send3DayMarketing();
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
        if(!empty($error))
            throw $error;
    }
}
