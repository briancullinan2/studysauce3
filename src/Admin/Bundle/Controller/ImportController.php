<?php


namespace Admin\Bundle\Controller;


use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\GroupInvite;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ImportController
 * @package Admin\Bundle\Controller
 */
class ImportController extends Controller
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        // get the groups this user has control over
        $groups = $user->getGroups()->toArray();
        if($user->hasRole('ROLE_ADMIN')) {
            $groups = $orm->getRepository('StudySauceBundle:Group')->createQueryBuilder('g')
                ->select('g')
                ->getQuery()
                ->getResult();
        }

        return $this->render('AdminBundle:Import:tab.html.php', ['groups' => $groups]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request)
    {
        set_time_limit(0);
        /** @var $userManager UserManager */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        /** @var User $user */
        $user = $this->getUser();

        $users = $request->get('users');
        $existing = $user->getGroupInvites()->toArray();
        $emails = new \StudySauce\Bundle\Controller\EmailsController();
        $emails->setContainer($this->container);
        foreach($users as $i => $u)
        {
            if(!empty($u['adviser'])) {
                /** @var User $adviser */
                $adviser = $userManager->findUserByEmail($u['adviser']);
                if(!empty($adviser)) {
                    $group = $adviser->getGroups()->first();
                }
                elseif(is_numeric($u['adviser'])) {
                    $group = $orm->getRepository('StudySauceBundle:Group')->createQueryBuilder('g')
                        ->select('g')
                        ->orWhere('g.id=:group')
                        ->setParameter('group', intval($u['adviser']))
                        ->setMaxResults(1)
                        ->getQuery()
                        ->getOneOrNullResult();
                }
                else {
                    $group = $orm->getRepository('StudySauceBundle:Group')->createQueryBuilder('g')
                        ->select('g')
                        ->where('g.name LIKE :search')
                        ->setParameter('search', '%' . $u['adviser'] . '%')
                        ->setMaxResults(1)
                        ->getQuery()
                        ->getOneOrNullResult();
                }
            }
            if(empty($group))
                $group = $user->getGroups()->first();

            unset($invite);
            // check if invite has already been sent
            foreach($existing as $j => $gi)
            {
                /** @var GroupInvite $gi */
                if(trim(strtolower($gi->getEmail())) == trim(strtolower($u['email']))) {
                    $invite = $gi;
                    break;
                }
            }

            // check if the user already exists
            /** @var User $invitee */
            $invitee = $userManager->findUserByEmail(trim($u['email']));

            // save the invite
            if((!isset($invite) || $invite->getCreated() < date_sub(new \DateTime(), new \DateInterval('P1D'))) && !empty($group)) {
                $invite = new GroupInvite();
                $invite->setGroup($group);
                $invite->setUser($user);
                $invite->setFirst($u['first']);
                $invite->setLast($u['last']);
                $invite->setEmail(trim($u['email']));
                $invite->setCode(md5(microtime()));
                $user->addGroupInvite($invite);
                $orm->persist($invite);
                $orm->flush();
                // don't send emails to existing users
                if(empty($invitee)) {
                    $emails->groupInviteAction($user, $invite);
                }
            }

            if(!empty($invitee)) {
                $invite->setStudent($invitee);
                $invite->setActivated(true);
                $invitee->addInvitedGroup($invite);
                if(!$invitee->hasGroup($group->getName()))
                    $invitee->addGroup($group);
                $userManager->updateUser($invitee);
                $orm->merge($invite);
                $orm->flush();
            }
        }

        return $this->forward('AdminBundle:Import:index', ['_format' => 'tab']);
    }

}