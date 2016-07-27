<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace StudySauce\Bundle\EventListener;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use FOS\UserBundle\Security\LoginManager;
use StudySauce\Bundle\Controller\HomeController;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\Entity\Visit;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

/**
 * AnonymousAuthenticationListener automatically adds a Token if none is
 * already present.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class AnonymousAuthenticationListener implements ListenerInterface
{
    /** @var $context \Symfony\Component\Security\Core\SecurityContext */
    private $context;

    private $logger;

    /** @var ContainerInterface $container */
    private $container;

    /**
     * @param SecurityContextInterface $context
     * @param $key
     * @param LoggerInterface $logger
     * @param ContainerInterface $container
     */
    public function __construct(
        SecurityContextInterface $context,
        $key,
        LoggerInterface $logger = null,
        ContainerInterface $container)
    {
        $this->context          = $context;
        $this->logger           = $logger;
        $this->container        = $container;
    }

    private static $randomNames = ['Pat', 'Jean', 'John', 'Will', 'Brian', 'Steve', 'Andrew', 'Roxie', 'Leland', 'Latrina', 'Mariette', 'Cheyenne', 'Page', 'Kerstin', 'Veda', 'Loni', 'Alexander', 'Rosalyn', 'Arvilla', 'Juliette', 'Delena', 'Natacha', 'Beatris', 'Lesia', 'Howard', 'Louetta', 'Buffy', 'Summer', 'Jannie', 'Emile', 'Dusti', 'Beverley', 'Hilma', 'Johnathan', 'Taisha', 'Ben', 'Teri', 'Latonya', 'Sadie', 'Elva', 'Mohammed', 'Slyvia', 'Syreeta', 'Evelynn', 'Kristle', 'Jessika', 'Rebbecca', 'Blair', 'Albertina', 'Isidro', 'Clarice', 'Lenore', 'Teresita', 'Stephani', 'Bruno', 'Gil', 'Dede'];

    /**
     * Handles anonymous authentication.
     *
     * @param GetResponseEvent $event A GetResponseEvent instance
     */
    public function handle(GetResponseEvent $event)
    {
        /** @var User $user */
        /** @var MessageDigestPasswordEncoder $encoder */

        $request = $event->getRequest();

        // only handle anonymous users with no context
        if (null !== ($token = $this->context->getToken()) && $token->isAuthenticated() &&
            $token->getUser() != 'anon.' && $request->get('_route') != 'demo' &&
            $request->get('_route') != 'demoadviser') {
            // reset Guest User for oauth connections
            $controller = $request->get('_controller');
            if(($controller == 'HWI\Bundle\OAuthBundle\Controller\ConnectController::connectServiceAction' ||
                    $controller == 'StudySauce\Bundle\Controller\AccountController::loginAction' ||
                    $controller == 'StudySauce\Bundle\Controller\AccountController::registerAction' ||
                    $controller == 'HWI\Bundle\OAuthBundle\Controller\ConnectController::redirectToServiceAction') &&
                ($user = $token->getUser()) !== null && ($user->hasRole('ROLE_GUEST') || $user->hasRole('ROLE_DEMO')))
            {
                $this->context->setToken(new AnonymousToken('main', 'anon.', []));
            }
            return;
        }

        $username = 'Guest' . ($request->get('_route') == 'demo' || $request->get('_route') == 'demoadviser'
                ? ('-' . substr(md5(microtime()), -5))
                : '');
        /** @var UserManager $userManager */
        $userManager = $this->container->get('fos_user.user_manager');
        /** @var EntityManager $orm */
        $orm = $this->container->get('doctrine')->getManager();
        /** @var Router $router */
        $router = $this->container->get('router');
        /** @var EncoderFactory $encoder */
        $encoder = $this->container->get('security.encoder_factory');
        $user = $userManager->findUserByUsername($username);
        if($user == null || $request->get('_route') == 'demo' || $request->get('_route') == 'demoadviser')
        {
            // generate a new guest user in the database
            $user = $userManager->createUser();
            $user->setUsername($username);
            $password = $encoder->getEncoder($user)->encodePassword('guest', $user->getSalt());
            $user->setPassword($password);
            $user->setEmail($username . '@mailinator.com');
            $userManager->updateCanonicalFields($user);
            if($request->get('_route') == 'demoadviser') {
                $user->addRole('ROLE_DEMO');
                $user->addRole('ROLE_PAID');
                $user->addRole('ROLE_ADVISER');
            }
            elseif($request->get('_route') == 'demo') {
                $user->addRole('ROLE_DEMO');
                $user->addRole('ROLE_PAID');
            }
            else
                $user->addRole('ROLE_GUEST');
            $user->setEnabled(true);
            $first = array_rand(self::$randomNames, 1);
            $last = rand(0, count(self::$randomNames) - 2);
            $user->setFirst(self::$randomNames[$first]);
            $user->setLast(self::$randomNames[($first + $last) % count(self::$randomNames)]);
            $orm->persist($user);
            $orm->flush();
        }

        $password = $encoder->getEncoder($user)->encodePassword('guest', $user->getSalt());
        $this->context->setToken(new UsernamePasswordToken($user, $password, 'main', $user->getRoles()));

        if (null !== $this->logger) {
            $this->logger->info('Populated SecurityContext with an anonymous Token');
        }

        if($request->get('_route') == 'demo' || $request->get('_route') == 'demoadviser')
        {
            if($user->hasRole('ROLE_DEMO') && $user->hasRole('ROLE_ADVISER')) {
                // reassign some random demo accounts to this new adviser account
                $demos = $orm->getRepository('StudySauceBundle:User')->createQueryBuilder('u')
                    ->distinct(true)
                    ->select('u')
                    ->where('u.roles LIKE \'%DEMO%\' AND u.roles NOT LIKE \'%ADVISER%\'')
                    ->leftJoin('u.partnerInvites', 'pi')
                    ->andWhere('pi.email IS NOT NULL')
                    //->andWhere('pi.email LIKE \'marketing@studysauce.com\'')
                    ->getQuery()
                    ->getResult();
                $randoms = array_rand($demos, min(10, count($demos)));
                foreach($randoms as $k => $i) {
                    /** @var User $demo */
                    $demo = $demos[$i];
                    /** @var PartnerInvite $pi */
                    $pi = $demo->getPartnerInvites()->first();
                    $pi->setPartner($user);
                    $pi->setActivated(true);
                    $pi->setEmail($user->getEmail());
                    $user->addInvitedPartner($pi);
                    $orm->merge($pi);

                    // create demo activity
                    $paths = ['/schedule', '/metrics', '/goals', '/plan', '/course1lesson1', '/account', '/premium', '/deadlines', '/checkin', '/home', '/partner', '/profile', '/calculator'];
                    $v = new Visit();
                    $v->setUser($demo);
                    $rand = array_rand($paths, 1);
                    $v->setPath($paths[$rand]);
                    $v->setHash('');
                    $v->setMethod('GET');
                    $v->setSession(md5(microtime()));
                    $user->addVisit($v);
                    $orm->persist($v);

                    if($k == count($randoms) - 1) {
                        $demo->setProperty('adviser_status', 'yellow');
                    }
                    elseif($k == count($randoms) - 2) {
                        $demo->setProperty('adviser_status', 'red');
                    }
                    else {
                        $demo->setProperty('adviser_status', 'green');
                    }
                    $orm->merge($demo);
                }

                $orm->flush();
            }
            elseif($user->hasRole('ROLE_GUEST') || $user->hasRole('ROLE_DEMO')) {

            }

            list($route, $options) = HomeController::getUserRedirect($user);
            $response = new RedirectResponse($router->generate($route, $options));
            /** @var LoginManager $loginManager */
            $loginManager = $this->container->get('fos_user.security.login_manager');
            $loginManager->logInUser('main', $user, $response);

            $event->setResponse($response);
        }

    }

}
