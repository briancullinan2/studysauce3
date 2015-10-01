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
use StudySauce\Bundle\Entity\Invite;
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
                $this->sendSpool();
            }
            catch (\Exception $e) {
                $error = $e;
            }
        }
        if(!$input->getOption('emails')) {

        }
        if(!empty($error))
            throw $error;
    }
}
