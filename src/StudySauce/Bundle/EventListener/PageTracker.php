<?php

namespace StudySauce\Bundle\EventListener;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use StudySauce\Bundle\Controller\LandingController;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\Entity\Visit;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Class RedirectListener
 */
class PageTracker implements EventSubscriberInterface
{
    /** @var EntityManager $orm */
    private $orm;

    /** @var Session $session */
    private $session;

    /** @var SecurityContext $context */
    private $context;

    /** @var ContainerInterface $container */
    private $container;

    /**
     * @param ContainerInterface $container
     * @param EntityManager $orm
     * @param Session $session
     * @param SecurityContext $context
     */
    public function __construct(ContainerInterface $container, EntityManager $orm, Session $session, SecurityContext $context)
    {
        $this->container = $container;
        $this->orm = $orm;
        $this->session = $session;
        $this->context = $context;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', -128]
        ];
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        // only do this for actual request, no sub requests
        if ($event->isMasterRequest()) {
            $request = $event->getRequest();
            $path = $request->getPathInfo();
            $visit = new Visit();
            $visit->setPath($path);
            $visit->setHash('');
            $visit->setMethod($request->getMethod());
            $visit->setIp(ip2long($request->server->get('REMOTE_ADDR')));
            if($this->session->isStarted())
                $id = $this->session->getId();

            /** @var TokenInterface $token */
            $token = $this->context->getToken();

            /** @var User $user */
            $user = $token->getUser();

            $query = $request->query->all();
            if(isset($query['__visits'])) {
                $visits = $query['__visits'];
                unset($query['__visits']);
            }
            $visit->setQuery(empty($query) ? null : $query);
            // don't bother saving requests to save the requests
            if($path != '/_visit' && $path != '/_fragment') {
                if (!empty($id)) {
                    $visit->setSession($id);
                }
                if ($user != 'anon.') {
                    $visit->setUser($user);
                    $user->addVisit($visit);
                    $user->setLastVisit(new \DateTime());
                    /** @var $userManager UserManager */
                    $userManager = $this->container->get('fos_user.user_manager');
                    $userManager->updateUser($user, false);
                }
                $this->orm->persist($visit);
                $this->orm->flush($visit);
            }
            // record visits leading up to this one
            if (isset($visits) && is_array($visits)) {
                foreach ($visits as $i => $v) {
                    $prev = new Visit();
                    $visited = new \DateTime($v['time']);
                    $visited->setTimezone(new \DateTimeZone(date_default_timezone_get()));
                    $prev->setCreated($visited);
                    $prev->setMethod($request->getMethod());
                    $prev->setIp(ip2long($request->server->get('REMOTE_ADDR')));
                    $prev->setHash(isset($v['hash']) ? $v['hash'] : '');
                    if(!empty($base = $request->getBaseUrl()) && strpos($v['path'], $base) == 0)
                        $v['path'] = substr($v['path'], strlen($request->getBaseUrl()));
                    if(substr($v['path'], 0, 1) != '/')
                        $v['path'] = '/' . $v['path'];
                    $prev->setPath($v['path']);
                    $prevQuery = self::queryToArray($v['query']);
                    $prev->setQuery(empty($prevQuery) ? null : $prevQuery);
                    if (!empty($id)) {
                        $prev->setSession($id);
                    }
                    if ($user != 'anon.') {
                        $prev->setUser($user);
                        $user->addVisit($prev);
                    }
                    $this->orm->persist($prev);
                }
            }
            // call visit action
            if($path != '/_visit') {
                $ctrl = new LandingController();
                $ctrl->setContainer($this->container);
                $ctrl->visitAction($request);
            }
            $this->orm->flush();
       }
    }

    /**
     * Source: http://snipplr.com/view/64010/
     * Parse out url query string into an associative array
     *
     * $qry can be any valid url or just the query string portion.
     * Will return false if no valid querystring found
     *
     * @param $qry String
     * @return Array
     */
    private static function queryToArray($qry)
    {
        $result = [];
        //string must contain at least one = and cannot be in first position
        if(strpos($qry,'=')) {

            if(strpos($qry,'?')!==false) {
                $q = parse_url($qry);
                $qry = $q['query'];
            }
        }else {
            return false;
        }

        foreach (explode('&', $qry) as $couple) {
            list ($key, $val) = explode('=', $couple);
            $result[$key] = $val;
        }

        return empty($result) ? false : $result;

    }
}