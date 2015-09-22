<?php

namespace StudySauce\Bundle\EventListener;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use FOS\UserBundle\Security\LoginManager;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\GroupInvite;
use StudySauce\Bundle\Entity\Invite;
use StudySauce\Bundle\Entity\ParentInvite;
use StudySauce\Bundle\Entity\PartnerInvite;
use StudySauce\Bundle\Entity\StudentInvite;
use StudySauce\Bundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

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
        if(empty($request->get('_code')))
            return;
        /** @var Session $session */
        $session = $request->getSession();
        /** @var $orm EntityManager */
        $orm = $this->container->get('doctrine')->getManager();
        /** @var PartnerInvite $partner */
        // TODO: merge with getInvite?
        $partner = $orm->getRepository('StudySauceBundle:PartnerInvite')->findOneBy(['code' => $request->get('_code')]);
        if(!empty($partner)) {
            $invite = $partner;
            self::$autoLogoutUser[$request->get('_code')] = function (Session $session) use ($request) {
                $session->set('partner', $request->get('_code'));
            };
        }
        /** @var ParentInvite $parent */
        $parent = $orm->getRepository('StudySauceBundle:ParentInvite')->findOneBy(['code' => $request->get('_code')]);
        if(!empty($parent)) {
            $invite = $parent;
            self::$autoLogoutUser[$request->get('_code')] = function (Session $session) use ($request) {
                $session->set('parent', $request->get('_code'));
            };
        }
        /** @var GroupInvite $group */
        $group = $orm->getRepository('StudySauceBundle:GroupInvite')->findOneBy(['code' => $request->get('_code')]);
        if(!empty($group)) {
            $invite = $group;
            self::$autoLogoutUser[$request->get('_code')] = function (Session $session) use ($request) {
                $session->set('group', $request->get('_code'));
            };
        }
        /** @var StudentInvite $student */
        $student = $orm->getRepository('StudySauceBundle:StudentInvite')->findOneBy(['code' => $request->get('_code')]);
        if(!empty($student)) {
            $invite = $student;
            self::$autoLogoutUser[$request->get('_code')] = function (Session $session) use ($request) {
                $session->set('student', $request->get('_code'));
            };
        }
        /** @var Invite $invite */
        if(!empty($invite)) {
            // set associations if user already exists
            /** @var User $user */
            $user = $userManager->findUserByEmail($invite->getEmail());
            if($user != null && !$user->hasRole('ROLE_GUEST') && !$user->hasRole('ROLE_DEMO')) {
                self::setInviteRelationship($orm, $request, $user);
                $userManager->updateUser($user);

                // automatically log in user
                $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
                $this->container->get('security.context')->setToken($token);
                $session->set('_security_main',serialize($token));

                $event->setResponse(new RedirectResponse($this->container->get('router')->generate('home')));
            }
            // redirect back to page with a fresh session
            elseif(null !== ($token = $this->container->get('security.context')->getToken()) && is_object($user = $token->getUser()) &&
                !$user->hasRole('ROLE_GUEST')) {
                // Logging user out.
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
            // just set up the session on guest or demo
            else {
                $setter = self::$autoLogoutUser[$request->get('_code')];
                $setter($session);
            }
        }
    }


    /**
     * @param EntityManager $orm
     * @param Request $request
     * @return \StudySauce\Bundle\Entity\GroupInvite|\StudySauce\Bundle\Entity\StudentInvite
     */
    public static function getInvite(EntityManager $orm, Request $request)
    {
        if(!empty($request->get('_code'))) {
            $code = $request->get('_code');
        }
        if(!empty($request->getSession()->get('partner'))) {
            $code = $request->getSession()->get('partner');
        }
        if(!empty($request->getSession()->get('parent'))) {
            $code = $request->getSession()->get('parent');
        }
        if(!empty($request->getSession()->get('student'))) {
            $code = $request->getSession()->get('student');
        }
        if(!empty($request->getSession()->get('group'))) {
            $code = $request->getSession()->get('group');
        }
        if(isset($code)) {
            /** @var PartnerInvite $partner */
            $partner = $orm->getRepository('StudySauceBundle:PartnerInvite')->findOneBy(['code' => $code]);
            if(!empty($partner)) return $partner;
            /** @var ParentInvite $parent */
            $parent = $orm->getRepository('StudySauceBundle:ParentInvite')->findOneBy(['code' => $code]);
            if(!empty($parent)) return $parent;
            /** @var GroupInvite $group */
            $group = $orm->getRepository('StudySauceBundle:GroupInvite')->findOneBy(['code' => $code]);
            if(!empty($group)) return $group;
            /** @var StudentInvite $student */
            $student = $orm->getRepository('StudySauceBundle:StudentInvite')->findOneBy(['code' => $code]);
            if(!empty($student)) return $student;
        }
        return null;
    }

    /**
     * @param EntityManager $orm
     * @param Request $request
     * @param User $user
     */
    public static function setInviteRelationship(EntityManager $orm, Request $request, User $user) {
        if(!$user->hasRole('ROLE_DEMO') && !$user->hasRole('ROLE_GUEST')) {
            $criteria = ['email' => $user->getEmail()];
        }
        if(!empty($request->get('_code'))) {
            $criteria = ['code' => $request->get('_code')];
        }
        if(!empty($request->getSession())) {
            if (!empty($request->getSession()->get('partner'))) {
                $user->addRole('ROLE_PARTNER');
                $criteria = ['code' => $request->getSession()->get('partner')];
            }
            if (!empty($request->getSession()->get('parent'))) {
                $user->addRole('ROLE_PARENT');
                $criteria = ['code' => $request->getSession()->get('parent')];
            }
            if (!empty($request->getSession()->get('student'))) {
                $criteria = ['code' => $request->getSession()->get('student')];
            }
            if (!empty($request->getSession()->get('group'))) {
                $criteria = ['code' => $request->getSession()->get('group')];
            }
        }

        if(isset($criteria)) {
            /** @var PartnerInvite $partner */
            $partner = $orm->getRepository('StudySauceBundle:PartnerInvite')->findOneBy($criteria);
            if (!empty($partner)) {
                $partner->setActivated(true);
                $partner->setPartner($user);
                $user->addInvitedPartner($partner);
                $orm->merge($partner);
            }
            /** @var ParentInvite $parent */
            $parent = $orm->getRepository('StudySauceBundle:ParentInvite')->findOneBy($criteria);
            if (!empty($parent)) {
                $parent->setActivated(true);
                $parent->setParent($user);
                $user->addInvitedParent($parent);
                $orm->merge($parent);
            }
            /** @var GroupInvite $group */
            $group = $orm->getRepository('StudySauceBundle:GroupInvite')->findOneBy($criteria);
            if (!empty($group)) {
                $group->setActivated(true);
                $group->setStudent($user);
                $user->addInvitedGroup($group);
                if(!$user->hasGroup($group->getGroup()->getName()))
                    $user->addGroup($group->getGroup());
                $orm->merge($group);
            }
            /** @var StudentInvite $student */
            $student = $orm->getRepository('StudySauceBundle:StudentInvite')->findOneBy($criteria);
            if (!empty($student)) {
                $student->setActivated(true);
                $student->setStudent($user);
                $user->addInvitedStudent($student);
                if ($student->getUser()->hasRole('ROLE_PARENT') || $student->getUser()->hasRole('ROLE_PARTNER')) {
                    if ($student->getUser()->hasRole('ROLE_PAID')) {
                        $user->addRole('ROLE_PAID');
                    }
                }
                $orm->merge($student);
            }
        }
        // assign correct group to anonymous users
        if(!empty($request->getSession()) && !empty($request->getSession()->get('organization'))) {
            /** @var Group $group */
            $group = $orm->getRepository('StudySauceBundle:Group')->findOneBy(['name' => $request->getSession()->get('organization')]);
            if(!$user->hasGroup($group->getName()))
                $user->addGroup($group);
        }
    }

}