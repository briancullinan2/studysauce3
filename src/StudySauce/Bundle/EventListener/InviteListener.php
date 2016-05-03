<?php

namespace StudySauce\Bundle\EventListener;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use FOS\UserBundle\Security\LoginManager;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Invite;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\Entity\UserPack;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Class RedirectListener
 */
class InviteListener implements EventSubscriberInterface
{
    /** @var ContainerInterface $container */
    protected $container;

    protected static $autoLogoutUser = [];


    /**
     * @param $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onInviteAccept', -100],
        ];
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onInviteAccept(GetResponseEvent $event)
    {
        /** @var $userManager UserManager */
        $userManager = $this->container->get('fos_user.user_manager');
        /** @var Request $request */
        $request = $event->getRequest();
        // TODO: only accept codes from landing pages?
        /** @var Session $session */
        $session = $request->getSession();
        if(empty($request->get('_code')))
            return;
        /** @var $orm EntityManager */
        $orm = $this->container->get('doctrine')->getManager();
        /** @var Invite $group */
        $invite = $orm->getRepository('StudySauceBundle:Invite')->findOneBy(['code' => $request->get('_code')]);
        /** @var Invite $invite */
        if(!empty($invite)) {
            $session->set('invite', $request->get('_code'));

            /** @var User $user */
            if (!empty($invite->getEmail())) {
                $user = $userManager->findUserByEmail($invite->getEmail());
            }
            if (!empty($invite->getInvitee())) {
                $user = $invite->getInvitee();
            }

            // set associations if user already exists
            if(!empty($user) && !$user->hasRole('ROLE_GUEST') && !$user->hasRole('ROLE_DEMO')) {
                self::setInviteRelationship($orm, $request, $user);
                $userManager->updateUser($user);

                // automatically log in user
                $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
                $this->container->get('security.context')->setToken($token);
                $session->set('_security_main',serialize($token));

                if($request->get('_route') != 'home' && $request->get('_route') != 'results') {
                    // TODO: redirect to all home urls?
                    $response = new RedirectResponse($this->container->get('router')->generate('home', ['_code' => $invite->getCode()]));
                    /** @var LoginManager $loginManager */
                    $loginManager = $this->container->get('fos_user.security.login_manager');
                    $loginManager->loginUser('main', $user, $response);
                    $event->setResponse($response);
                }
            }
            // redirect back to page with a fresh session
            /** @var TokenInterface $token */
            elseif($request->get('_route') != 'account_create' && null !== ($token = $this->container->get('security.context')->getToken()) && is_object($user = $token->getUser()) &&
                !$user->hasRole('ROLE_GUEST')) {
                // Already logged in as another user, logging user out.
                $this->container->get('security.context')->setToken(null);
                // Invalidating the session.
                $request->getSession()->invalidate();
                $response = new RedirectResponse($request->server->get('REQUEST_URI'));
                // Clearing the cookies.
                if (null !== $response) {
                    foreach ([
                                 'PHPSESSID',
                                 'REMEMBERME',
                             ] as $cookieName) {
                        $response->headers->clearCookie($cookieName);
                    }
                }
                $event->setResponse($response);
            }
            // session already set for guest and demo
        }
    }


    /**
     * @param EntityManager $orm
     * @param Request $request
     * @return \StudySauce\Bundle\Entity\Invite
     */
    public static function getInvite(EntityManager $orm, Request $request)
    {
        if(!empty($request->getSession()->get('invite'))) {
            $code = $request->getSession()->get('invite');
        }
        if(!empty($request->get('_code'))) {
            $code = $request->get('_code');
        }
        if(isset($code)) {
            /** @var Invite $partner */
            $invite = $orm->getRepository('StudySauceBundle:Invite')->findOneBy(['code' => $code]);
            if(!empty($invite)) return $invite;
        }
        return null;
    }

    /**
     * @param EntityManager $orm
     * @param Request $request
     * @param User $user
     * @return null|Invite
     */
    public static function setInviteRelationship(EntityManager $orm, Request $request, User $user) {
        if(!$user->hasRole('ROLE_DEMO') && !$user->hasRole('ROLE_GUEST')) {
            $criteria = ['email' => $user->getEmail()];
        }
        if(!empty($request->getSession())) {
            if (!empty($request->getSession()->get('invite'))) {
                $criteria = ['code' => $request->getSession()->get('invite')];
            }
        }
        if(!empty($request->get('_code'))) {
            $criteria = ['code' => $request->get('_code')];
        }

        if(isset($criteria)) {
            /** @var Invite $invite */
            $invite = $orm->getRepository('StudySauceBundle:Invite')->findOneBy($criteria);
            if (!empty($invite)) {
                $invite->setActivated(true);
                // only set invitee on single use invites, multi-use invites have no email address
                if(!empty($invite->getEmail())) {
                    $invite->setInvitee($user);
                    $user->addInvitee($invite);
                }
                if(!empty($invite->getGroup()) && !$user->hasGroup($invite->getGroup()->getName())) {
                    $user->addGroup($invite->getGroup());
                }
                if(!empty($invite->getPack()) && empty($user->getUserPacks()->filter(function (UserPack $x) use ($invite) {return $x->getPack() == $invite->getPack();})->first())) {
                    $up = new UserPack();
                    $up->setUser($user);
                    $up->setPack($invite->getPack());
                    $user->addUserPack($up);
                    $orm->persist($up);
                }
                $orm->merge($invite);
            }

        }
        // assign correct group to anonymous users
        if(!empty($request->getSession()) && !empty($request->getSession()->get('organization'))) {
            /** @var Group $group */
            $group = $orm->getRepository('StudySauceBundle:Group')->findOneBy(['name' => $request->getSession()->get('organization')]);
            if(!$user->hasGroup($group->getName()))
                $user->addGroup($group);
        }

        return isset($invite) ? $invite : null;
    }

}