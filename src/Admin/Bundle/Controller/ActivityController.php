<?php

namespace Admin\Bundle\Controller;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManager;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\Entity\Visit;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class PartnerController
 * @package StudySauce\Bundle\Controller
 */
class ActivityController extends Controller
{

    /**
     * @param Request $request
     * @return array
     */
    public function indexAction(Request $request)
    {

        /** @var $user User */
        $user = $this->getUser();
        if(!$user->hasRole('ROLE_ADMIN')) {
            throw new AccessDeniedHttpException();
        }

        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        $start = new \DateTime('today');
        if(!empty($request->get('start')))
            $start->setTimestamp(intval($request->get('start')));
        $end = new \DateTime('now');
        if(!empty($request->get('end')))
            $end->setTimestamp(intval($request->get('end')));
        /** @var QueryBuilder $entities */
        $entities = $orm->getRepository('StudySauceBundle:Visit')->createQueryBuilder('v')
            ->distinct()
            ->select(['v', 'u'])
            ->leftJoin('v.user', 'u')
            ->leftJoin('u.groups', 'g')
            ->where('v.created > :start AND v.created < :end' . (!empty($request->get('not')) ? (' AND v.id NOT IN (' . $request->get('not') . ')') : ''))
            ->andWhere('u.roles NOT LIKE \'%s:10:"ROLE_ADMIN"%\' AND v.path != \'/cron\'');
        if(!empty($request->get('search'))) {
            if($request->get('search') == 'New session') {
                $entities = $entities->andWhere('v.session IS NULL');
            }
            elseif(strlen($request->get('search')) == 26) {
                $entities = $entities->andWhere('v.session=:sess')
                    ->setParameter('sess', trim($request->get('search')));
            }
            elseif(!empty(ip2long($request->get('search')))) {
                $entities = $entities->andWhere('v.ip=:ip')
                    ->setParameter('ip', ip2long($request->get('search')));
            }
            elseif(substr($request->get('search'), 0, 1) == '/') {
                $entities = $entities->andWhere('v.path LIKE :path')
                    ->setParameter('path', $request->get('search') . '%');
            }
            elseif(strpos($request->get('search'), '@') !== false) {
                $entities = $entities->andWhere('OR u.email LIKE :email')
                    ->setParameter('email', '%' . $request->get('search') . '%');
            }
            elseif(is_numeric($request->get('search'))) {
                $entities = $entities->andWhere('u.id=:id')
                    ->setParameter('id', intval($request->get('search')));
            }
            else {
                $entities = $entities->andWhere('u.email LIKE \'%' . $request->get('search') . '%\' OR u.first LIKE \'%' . $request->get('search') . '%\' OR u.last LIKE \'%' . $request->get('search') . '%\' OR g.name LIKE \'%' . $request->get('search') . '%\' OR g.description LIKE \'%' . $request->get('search') . '%\'');
            }
        }
        $entities = $entities
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('v.created', 'DESC')
            ->setFirstResult(0)
            ->setMaxResults(100)
            ->getQuery()
            ->getResult();
        /** @var array $entities */

        $visits = array_map(function (Visit $v) {
            return [
                'id' => $v->getId(),
                'start' => $v->getCreated()->format('r'),
                'content' => '<div data-id="' . $v->getId() . '">' .
                    '<a target="_blank" href="https://' . $_SERVER['HTTP_HOST']
                        . $v->getPath() . '"><strong>' . $v->getMethod() . '</strong><span> ' . $v->getPath() . '</span></a><br />' .
                    (!empty($v->getQuery()) ? ('<a href="#search-' . (!empty($v->getUser()) ? $v->getUser()->getId() : '') . '"><strong>Query:</strong><span> ' . implode('<strong> &amp; </strong>', array_map(function ($v, $k) {return $k . '=' . $v;}, $v->getQuery(), array_keys($v->getQuery()))) . '</span></a><br />') : '') .
                    '<a href="#search-' . (!empty($v->getUser()) ? $v->getUser()->getEmail() : 'Guest') . '"><strong>User:</strong><span> ' . (!empty($v->getUser()) ? $v->getUser()->getEmail() : 'Guest') . '</span></a><br />' .
                    '<a href="#search-' . (!empty($v->getSession()) ? $v->getSession() : 'New session') . '"><strong>Session Id:</strong><span> ' . (!empty($v->getSession()) ? $v->getSession() : 'New session') . '</span></a><br />' .
                    '<a href="#search-' . long2ip($v->getIp()) . '"><strong>IP:</strong><span> ' . long2ip($v->getIp()) . '</span></a><br />' .
                    '</div>',
                'className' => 'session-id-' . (!empty($v->getSession()) ? $v->getSession() : '') . ' user-id-' . (!empty($v->getUser()) && !$v->getUser()->hasRole('ROLE_GUEST') && !$v->getUser()->hasRole('ROLE_DEMO') ? $v->getUser()->getId() : '')
            ];
        }, $entities);

        if($request->isXmlHttpRequest() && !empty($request->get('start')) && !empty($request->get('end'))) {
            return new JsonResponse($visits);
        }

        return $this->render('AdminBundle:Activity:tab.html.php', [
            'visits' => $visits
        ]);
    }
}