<?php


namespace Admin\Bundle\Controller;


use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Invite;
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

    static public function getSimpleCode() {
        return md5(microtime());
    }

    static public function getAbbreviationCode(Group $g, EntityManager $orm) {
        preg_match_all('/\s[a-z]|[A-Z]/', $g->getName(), $matches);
        $prefix = implode('', array_map(function ($x) { return trim($x); }, $matches[0]));
        $random = substr(md5(microtime()), -4);
        // TODO: make sure it doesn't already exist in the database
        return  strtoupper($prefix . $random);
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
        $existing = $user->getInvites()->toArray();
        $emails = new \StudySauce\Bundle\Controller\EmailsController();
        $emails->setContainer($this->container);
        foreach($users as $i => $u)
        {
            $group = null;
            if(!empty($u['group'])) {
                /** @var User $adviser */
                $adviser = $userManager->findUserByEmail($u['group']);
                if(!empty($adviser)) {
                    /** @var Group $group */
                    $group = $adviser->getGroups()->first();
                }
                elseif(is_numeric($u['group'])) {
                    $group = $orm->getRepository('StudySauceBundle:Group')->createQueryBuilder('g')
                        ->select('g')
                        ->orWhere('g.id=:group')
                        ->setParameter('group', intval($u['group']))
                        ->setMaxResults(1)
                        ->getQuery()
                        ->getOneOrNullResult();
                }
                else {
                    $group = $orm->getRepository('StudySauceBundle:Group')->createQueryBuilder('g')
                        ->select('g')
                        ->where('g.name LIKE :search')
                        ->setParameter('search', '%' . $u['group'] . '%')
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
                if(empty(trim($u['email']))) {
                    continue;
                }
                /** @var Invite $gi */
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
                $invite = new Invite();
                $invite->setGroup($group);
                $group->addInvite($invite);
                $invite->setUser($user);
                $user->addInvite($invite);
                $invite->setFirst($u['first']);
                $invite->setLast($u['last']);
                $invite->setEmail(trim($u['email']));
                $invite->setCode(static::getAbbreviationCode($group, $orm));
                $orm->persist($invite);
                $orm->flush();
                // don't send emails to existing users
                if(empty($invitee) && !empty($invite->getEmail())) {
                    $emails->inviteAction($user, $invite);
                }
            }

            if(isset($invite) && !empty($invitee)) {
                $invite->setInvitee($invitee);
                $invite->setActivated(true);
                $invitee->addInvitee($invite);
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