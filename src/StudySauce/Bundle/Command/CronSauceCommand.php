<?php

namespace StudySauce\Bundle\Command;

use Doctrine\ORM\EntityManager;
use Exception;
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
            ->where('g.activated=0 AND g.invitee IS NULL AND g.user IS NOT NULL')
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
            ->where('u.devices IS NOT NULL AND u.devices != \'\'')
            ->getQuery()->getResult();

        $controller = new PacksController();
        $controller->setContainer($this->getContainer());
        $emails = new EmailsController();
        $emails->setContainer($this->getContainer());

        foreach($users as $u) {
            /** @var User $u */
            // don't send to child accounts unless they have set their own email address
            if($u->getInvitees()->filter(function (Invite $i) use ($u) {
                    return $i->getUser()->getEmail() . '_' == substr($u->getEmail(), 0, strlen($i->getUser()->getEmail()) + 1);})->count() > 0) {
                continue;
            }

            $packs = $controller->getPacksForUser($u);

            $notify = [];
            // loop through packs and determine if they have already been downloaded by the user
            foreach($packs as $p) {

                $children = $p->getChildUsers($u);

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
                    if ($p->isNewForChild($c)) {
                        $notify[] = [$p, $c];
                    }
                }
            }

            /** @var Pack[] $difference */
            $difference = [];
            $unique = [];
            foreach($notify as list($p, $c)) {
                if(!in_array($p->getId(), $unique)) {
                    $unique[] = $p->getId();
                }
                if (!in_array($p->getId(), $u->getProperty('notified') ?: [])) {
                    $difference[] = $p;
                }
            }

            if (count($difference) > 0) {
                print "\n" . $u->getEmail();

                $u->setProperty('notified', array_unique(array_merge(array_map(function ($n) {
                    /** @var Pack $p */
                    list($p) = $n;
                    return $p->getId(); }, $notify), $u->getProperty('notified') ?: [])));
                $this->getContainer()->get('fos_user.user_manager')->updateUser($u);

                $groupInvite = null;
                $child = array_values(array_filter($notify, function ($n) use ($u) {
                    /** @var User $child */
                    /** @var Pack $pack */
                    list($pack, $child) = $n;
                    return $child != $u && $pack->getGroups()->filter(function (Group $i) use ($child) {
                        return $child->hasGroup($i->getName());})->count() > 0;
                }));

                /** @var Group $groupInvite */
                if(!empty($child)) {
                    /** @var User $childUser */
                    /** @var Pack $childPack */
                    list($childPack, $childUser) = $child[0];
                    $groupInvite = $childPack->getGroupForChild($childUser);
                }

                /** @var Pack[] $alerting */
                $alerting = array_values(array_filter($difference, function (Pack $p) {
                    return $p->getProperty('alert') == true;
                }));

                // send notifications to all users devices
                // TODO: select child invite for packs with alerts, and separately packs with emails
                if (count($alerting) > 0) {
                    foreach($u->getDevices() as $d) {
                        if (!empty($groupInvite)) {
                            print "\t" . $groupInvite->getName() . ' added a new pack, "' . $alerting[0]->getTitle() . '"';
                            $this->sendNotification($groupInvite->getName() . ' added a new pack, "'
                                . $alerting[0]->getTitle() . '"', count($unique), str_replace([' ', '<', '>'], '', $d));
                        }
                        else {
                            print "\t" . 'You have a new pack "' . $alerting[0]->getTitle() . '" on Study Sauce';
                            $this->sendNotification('You have a new pack "' . $alerting[0]->getTitle()
                                . '" on Study Sauce', count($unique), str_replace([' ', '<', '>'], '', $d));
                        }
                    }
                }

                /** @var Pack[] $emailing */
                $emailing = array_values(array_filter($difference, function (Pack $p) {
                    return $p->getProperty('email') == true;
                }));

                if(count($emailing) > 0) {
                    if(!filter_var($u->getEmail(), FILTER_VALIDATE_EMAIL)) {
                        continue;
                    }
                    print "\t" . 'We have added ' . $emailing[0]->getTitle() . ' to Study Sauce';
                    $emails->sendNewPacksNotification($u, $emailing, !empty($groupInvite) ? $groupInvite : null, !empty($child) ? $child[0][1] : null);
                }
            }
        }
    }

    public function sendNotification($message, $count, $deviceToken) {
        try {
            $body['aps'] = array(
                'alert' => $message,
                'badge' => $count
            );

            //$body['category'] = 'message';
            //$body['category'] = 'profile';
            //$body['category'] = 'dates';
            //$body['category'] = 'daily_dates';
            //$body['sender'] = 'jamesHAW';
            $body['sender'] = 'web.StudySauce';

            //Server stuff
            $ctx = stream_context_create();
            stream_context_set_option($ctx, 'ssl', 'local_cert', __DIR__ . '/' . 'com.studysauce.companyapp.pem');
            $fp = stream_socket_client(
                'ssl://gateway' . ($this->getContainer()->get('kernel')->getEnvironment() == 'prod' ? '' : '.sandbox') . '.push.apple.com:2195', $err,
                $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
            if (!$fp)
                throw new Exception("Failed to connect: $err $errstr" . PHP_EOL);
            $this->getContainer()->get('logger')->debug('Connected to APNS' . PHP_EOL);
            $payload = json_encode($body);

            // Build the binary notification
            $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

            // Send it to the server
            $result = fwrite($fp, $msg, strlen($msg));
            if (!$result)
                throw new Exception('Message not delivered' . PHP_EOL);
            else
                $this->getContainer()->get('logger')->debug('Message successfully delivered' . PHP_EOL);
            fclose($fp);
        }
        catch (Exception $e) {
            $this->getContainer()->get('logger')->debug($e);
        }

    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // set the timeout to 4 and a half minutes
        $options = $input->getOptions();
        $empty = !$options['emails'] && !$options['sync'] && !$options['notify'];
        set_time_limit(60*6);
        if($empty || !empty($options['sync'])) {

        }
        if($empty || !empty($options['notify'])) {
            $this->sendNotifications();
        }
        if($empty || !empty($options['emails'])) {
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
