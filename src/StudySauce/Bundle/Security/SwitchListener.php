<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace StudySauce\Bundle\Security;


use FOS\UserBundle\Security\EmailUserProvider;
use FOS\UserBundle\Security\LoginManager;
use StudySauce\Bundle\Entity\Invite;
use StudySauce\Bundle\Entity\User;
use Symfony\Component\Security\Http\Firewall\SwitchUserListener;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Role\SwitchUserRole;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Event\SwitchUserEvent;
use Symfony\Component\Security\Http\RememberMe\RememberMeServicesInterface;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * SwitchUserListener allows a user to impersonate another one temporarily
 * (like the Unix su command).
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class SwitchListener extends SwitchUserListener
{
    /** @var RememberMeServicesInterface $loginManager */
    private $loginManager;
    private $tokenStorage;
    /** @var EmailUserProvider $provider */
    private $provider;
    private $userChecker;
    private $providerKey;
    private $accessDecisionManager;
    private $usernameParameter;
    private $role;
    private $logger;
    private $dispatcher;

    public function __construct(TokenStorageInterface $tokenStorage, UserProviderInterface $provider, UserCheckerInterface $userChecker, $providerKey, AccessDecisionManagerInterface $accessDecisionManager, LoggerInterface $logger = null, $usernameParameter = '_switch_user', $role = 'ROLE_ALLOWED_TO_SWITCH', EventDispatcherInterface $dispatcher = null, RememberMeServicesInterface $loginManager)
    {
        if (empty($providerKey)) {
            throw new \InvalidArgumentException('$providerKey must not be empty.');
        }

        $this->loginManager = $loginManager;
        $this->tokenStorage = $tokenStorage;
        $this->provider = $provider;
        $this->userChecker = $userChecker;
        $this->providerKey = $providerKey;
        $this->accessDecisionManager = $accessDecisionManager;
        $this->usernameParameter = $usernameParameter;
        $this->role = $role;
        $this->logger = $logger;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Handles the switch to another user.
     *
     * @param GetResponseEvent $event A GetResponseEvent instance
     *
     * @throws \LogicException if switching to a user failed
     */
    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (!$request->get($this->usernameParameter)) {
            return;
        }

        if ('_exit' === $request->get($this->usernameParameter)) {
            try {
                $this->tokenStorage->setToken($this->attemptExitUser($request));
            }
            catch (AuthenticationException $e) {

            }
        } else {
            try {
                $this->tokenStorage->setToken($this->attemptSwitchUser($request));
            } catch (AuthenticationException $e) {
                throw new \LogicException(sprintf('Switch User failed: "%s"', $e->getMessage()));
            }
        }

        $request->query->remove($this->usernameParameter);
        $request->server->set('QUERY_STRING', http_build_query($request->query->all()));

        $response = new RedirectResponse($request->getUri(), 302);

        $event->setResponse($response);
        /** @var LoginManager $loginManager */
        $this->provider->refreshUser($this->tokenStorage->getToken()->getUser());
        $request->query->set('_remember_me', true);
        $this->loginManager->loginSuccess($request, $response, $this->tokenStorage->getToken());
        //$loginManager = $this->get('fos_user.security.login_manager');
        //$loginManager->logInUser('main', $user, $response);
    }

    /**
     * Attempts to switch to another user.
     *
     * @param Request $request A Request instance
     *
     * @return TokenInterface|null The new TokenInterface if successfully switched, null otherwise
     *
     * @throws \LogicException
     * @throws AccessDeniedException
     */
    private function attemptSwitchUser(Request $request)
    {
        $token = $this->tokenStorage->getToken();
        $originalToken = $this->getOriginalToken($token);

        if (false !== $originalToken) {
            if ($token->getUsername() === $request->get($this->usernameParameter)) {
                return $token;
            }

            // switch back and attempt new switch
            if (null !== $this->dispatcher) {
                $this->tokenStorage->setToken($this->attemptExitUser($request));
            }
            else {
                throw new \LogicException(sprintf('You are already switched to "%s" user.', $token->getUsername()));
            }
        }

        if (false === $this->accessDecisionManager->decide($token, array($this->role))) {
            throw new AccessDeniedException();
        }

        $username = $request->get($this->usernameParameter);

        if (null !== $this->logger) {
            $this->logger->info('Attempting to switch to user.', array('username' => $username));
        }

        $user = $this->provider->loadUserByUsername($username);

        // decide based on connected users
        /** @var User $originalUser */
        $originalUser = $this->provider->loadUserByUsername($token->getUser()->getUsername());
        if(false == ($originalUser->hasRole('ROLE_ADMIN') || $originalUser->getInvites()->exists(function ($_, Invite $i) use ($user) {return $i->getInvitee() == $user;})
            || $originalUser->getParent() == $user)) {
            throw new AccessDeniedException();
        }

        $this->userChecker->checkPostAuth($user);

        $roles = $user->getRoles();
        $roles[] = new SwitchUserRole('ROLE_PREVIOUS_ADMIN', $this->tokenStorage->getToken());

        $token = new UsernamePasswordToken($user, $user->getPassword(), $this->providerKey, $roles);

        if (null !== $this->dispatcher) {
            $switchEvent = new SwitchUserEvent($request, $token->getUser());
            $this->dispatcher->dispatch(SecurityEvents::SWITCH_USER, $switchEvent);
        }

        return $token;
    }

    /**
     * Attempts to exit from an already switched user.
     *
     * @param Request $request A Request instance
     *
     * @return TokenInterface The original TokenInterface instance
     *
     * @throws AuthenticationCredentialsNotFoundException
     */
    private function attemptExitUser(Request $request)
    {
        if (false === $original = $this->getOriginalToken($this->tokenStorage->getToken())) {
            throw new AuthenticationCredentialsNotFoundException('Could not find original Token object.');
        }

        if (null !== $this->dispatcher) {
            $user = $this->provider->refreshUser($original->getUser());
            $switchEvent = new SwitchUserEvent($request, $user);
            $this->dispatcher->dispatch(SecurityEvents::SWITCH_USER, $switchEvent);
        }

        return $original;
    }

    /**
     * Gets the original Token from a switched one.
     *
     * @param TokenInterface $token A switched TokenInterface instance
     *
     * @return TokenInterface|false The original TokenInterface instance, false if the current TokenInterface is not switched
     */
    private function getOriginalToken(TokenInterface $token)
    {
        foreach ($token->getRoles() as $role) {
            if ($role instanceof SwitchUserRole) {
                return $role->getSource();
            }
        }

        return false;
    }
}
