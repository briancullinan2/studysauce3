<?php

namespace Admin\Bundle\Controller;

use Aws\Sns\Exception\NotFoundException;
use Course1\Bundle\Entity\Course1;
use Course2\Bundle\Entity\Course2;
use Course3\Bundle\Entity\Course3;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use StudySauce\Bundle\Controller\HomeController;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Templating\DelegatingEngine;
use Symfony\Bundle\FrameworkBundle\Templating\TimedPhpEngine;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Templating\Helper\SlotsHelper;

/**
 * Class PartnerController
 * @package StudySauce\Bundle\Controller
 */
class ResultsController extends Controller
{
    private static $paidStr = '';

    /**
     * @param Request $request
     * @param $joins
     * @return QueryBuilder
     */
    private function searchBuilder(Request $request, &$joins = [])
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        /** @var User $user */
        $user = $this->getUser();

        $joins = [];
        /** @var QueryBuilder $qb */
        $qb = $orm->getRepository('StudySauceBundle:User')->createQueryBuilder('u');
        if(!$user->hasRole('ROLE_ADMIN')) {
            if($user->hasRole('ROLE_ADVISER')) {
                // TODO: show users in this advisers group, convert to IN statement
            }
            $qb = $qb->andWhere('u.id=:user_id')
                ->setParameter('user_id', $user->getId());
        }

        if (empty(self::$paidStr)) {
            $paidGroups = $orm->getRepository('StudySauceBundle:Group')->createQueryBuilder('g')
                ->select('g.id')
                ->andWhere('g.roles LIKE \'%s:9:"ROLE_PAID"%\'')
                ->getQuery()
                ->getArrayResult();
            self::$paidStr = implode(', ', array_map(function ($x) {
                return $x['id'];
            }, $paidGroups));
        }


        if (!empty($search = $request->get('search'))) {
            if (strpos($search, '%') === false) {
                $search = '%' . $search . '%';
            }
            if (!in_array('g', $joins)) {
                $qb = $qb->leftJoin('u.groups', 'g');
                $joins[] = 'g';
            }
            $qb = $qb->andWhere('u.first LIKE :search OR u.last LIKE :search OR u.email LIKE :search OR g.name LIKE :search OR g.description LIKE :search')
                ->setParameter('search', $search);
        }

        if (!empty($role = $request->get('role'))) {
            if ($role == 'ROLE_STUDENT') {
                $qb = $qb->andWhere('u.roles NOT LIKE \'%s:12:"ROLE_ADVISER"%\' AND u.roles NOT LIKE \'%s:19:"ROLE_MASTER_ADVISER"%\' AND u.roles NOT LIKE \'%s:12:"ROLE_PARTNER"%\' AND u.roles NOT LIKE \'%s:11:"ROLE_PARENT"%\'');
            } else if ($role == 'ROLE_PAID') {
                if (!in_array('g', $joins)) {
                    $qb = $qb->leftJoin('u.groups', 'g');
                    $joins[] = 'g';
                }
                $qb = $qb->andWhere('u.roles LIKE \'%s:9:"ROLE_PAID"%\' OR g.id IN (' . self::$paidStr . ')');
            } else {
                $qb = $qb->andWhere('u.roles LIKE \'%s:' . strlen($role) . ':"' . $role . '"%\'');
            }
        }

        if (!empty($group = $request->get('group'))) {
            if (!in_array('g', $joins)) {
                $qb = $qb->leftJoin('u.groups', 'g');
                $joins[] = 'g';
            }
            if ($group == 'nogroup') {
                $qb = $qb->andWhere('g.id IS NULL');
            } else {
                $qb = $qb->andWhere('g.id=:gid')->setParameter('gid', intval($group));
            }
        }

        if (!empty($last = $request->get('last'))) {
            $qb = $qb->andWhere('u.last LIKE \'' . $last . '\'');
        }

        if (!empty($completed = $request->get('completed'))) {
            if (!in_array('c1', $joins)) {
                $qb = $qb
                    ->leftJoin('u.course1s', 'c1')
                    ->leftJoin('u.course2s', 'c2')
                    ->leftJoin('u.course3s', 'c3');
                $joins[] = 'c1';
            }
            if (($pos = strpos($completed, '1')) !== false) {
                if (substr($completed, $pos - 1, 1) != '!')
                    $qb = $qb->andWhere('c1.lesson1=4 AND c1.lesson2=4 AND c1.lesson3=4 AND c1.lesson4=4 AND c1.lesson5=4 AND c1.lesson6=4');
                else
                    $qb = $qb->andWhere('(c1.lesson1<4 OR c1.lesson1 IS NULL) OR (c1.lesson2<4 OR c1.lesson2 IS NULL) OR (c1.lesson3<4 OR c1.lesson3 IS NULL) OR (c1.lesson4<4 OR c1.lesson4 IS NULL) OR (c1.lesson5<4 OR c1.lesson5 IS NULL) OR (c1.lesson6<4 OR c1.lesson6 IS NULL)');
            }
            if (($pos = strpos($completed, '2')) !== false) {
                if (substr($completed, $pos - 1, 1) != '!')
                    $qb = $qb->andWhere('c2.lesson1=4 AND c2.lesson2=4 AND c2.lesson3=4 AND c2.lesson4=4 AND c2.lesson5=4');
                else
                    $qb = $qb->andWhere('(c2.lesson1<4 OR c2.lesson1 IS NULL) OR (c2.lesson2<4 OR c2.lesson2 IS NULL) OR (c2.lesson3<4 OR c2.lesson3 IS NULL) OR (c2.lesson4<4 OR c2.lesson4 IS NULL) OR (c2.lesson5<4 OR c2.lesson5 IS NULL)');
            }
            if (($pos = strpos($completed, '3')) !== false) {
                if (substr($completed, $pos - 1, 1) != '!')
                    $qb = $qb->andWhere('c3.lesson1=4 AND c3.lesson2=4 AND c3.lesson3=4 AND c3.lesson4=4 AND c3.lesson5=4');
                else
                    $qb = $qb->andWhere('(c3.lesson1<4 OR c3.lesson1 IS NULL) OR (c3.lesson2<4 OR c3.lesson2 IS NULL) OR (c3.lesson3<4 OR c3.lesson3 IS NULL) OR (c3.lesson4<4 OR c3.lesson4 IS NULL) OR (c3.lesson5<4 OR c3.lesson5 IS NULL)');
            }
        }

        if (!empty($paid = $request->get('paid'))) {
            if (!in_array('g', $joins)) {
                $qb = $qb->leftJoin('u.groups', 'g');
                $joins[] = 'g';
            }
            if ($paid == 'yes') {
                $qb = $qb->andWhere('u.roles LIKE \'%s:9:"ROLE_PAID"%\' OR g.id IN (' . self::$paidStr . ')');
            } else {
                $qb = $qb->andWhere('u.roles NOT LIKE \'%s:9:"ROLE_PAID"%\' AND g.id NOT IN (' . self::$paidStr . ')');
            }
        }

        return $qb;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function indexAction(Request $request)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        /** @var User $user */
        $user = $this->getUser();
        list($route, $options) = HomeController::getUserRedirect($user);
        if($route != 'home' && $route != 'results')
            return $this->redirect($this->generateUrl($route, $options));

        set_time_limit(0);

        // count total so we know the max pages
        $total = self::searchBuilder($request)
            ->select('COUNT(DISTINCT u.id)')
            ->getQuery()
            ->getSingleScalarResult();

        // max pagination to search count
        if (!empty($page = $request->get('page'))) {
            if ($page == 'last') {
                $page = $total / 25;
            }
            $resultOffset = (min(max(1, ceil($total / 25)), max(1, intval($page))) - 1) * 25;
        } else {
            $resultOffset = 0;
        }

        // get the actual list of users
        /** @var QueryBuilder $users */
        $users = self::searchBuilder($request, $joins)->distinct(true)->select('u');

        // figure out how to sort
        if (!empty($order = $request->get('order'))) {
            $field = explode(' ', $order)[0];
            $direction = explode(' ', $order)[1];
            if ($direction != 'ASC' && $direction != 'DESC')
                $direction = 'DESC';
            // no extra join information needed
            if ($field == 'created' || $field == 'lastLogin' || $field == 'lastVisit' || $field == 'last') {
                $users = $users->orderBy('u.' . $field, $direction);
            }

        } else {
            $users = $users->orderBy('u.lastVisit', 'DESC');
        }

        $users = $users
            ->setFirstResult($resultOffset)
            ->setMaxResults(25)
            ->getQuery()
            ->getResult();

        // TODO: get aggregate data for every quiz answer


        $parents = self::searchBuilder($request)
            ->select('COUNT(DISTINCT u.id)')
            ->andWhere('u.roles LIKE \'%s:11:"ROLE_PARENT"%\'')
            ->getQuery()
            ->getSingleScalarResult();

        $partners = self::searchBuilder($request)
            ->select('COUNT(DISTINCT u.id)')
            ->andWhere('u.roles LIKE \'%s:12:"ROLE_PARTNER"%\'')
            ->getQuery()
            ->getSingleScalarResult();

        $advisers = self::searchBuilder($request)
            ->select('COUNT(DISTINCT u.id)')
            ->andWhere('u.roles LIKE \'%s:12:"ROLE_ADVISER"%\' OR u.roles LIKE \'%s:19:"ROLE_MASTER_ADVISER"%\'')
            ->getQuery()
            ->getSingleScalarResult();

        $students = self::searchBuilder($request)
            ->select('COUNT(DISTINCT u.id)')
            ->andWhere('u.roles NOT LIKE \'%s:12:"ROLE_ADVISER"%\'')
            ->andWhere('u.roles NOT LIKE \'%s:19:"ROLE_MASTER_ADVISER"%\'')
            ->andWhere('u.roles NOT LIKE \'%s:12:"ROLE_PARTNER"%\'')
            ->andWhere('u.roles NOT LIKE \'%s:11:"ROLE_PARENT"%\'')
            ->getQuery()
            ->getSingleScalarResult();

        /** @var QueryBuilder $torch */
        $torch = self::searchBuilder($request, $joins);
        if (!in_array('g', $joins)) {
            $torch = $torch->leftJoin('u.groups', 'g');
        }
        $torch = $torch->select('COUNT(DISTINCT u.id)')
            ->andWhere('g.name LIKE \'%torch%\'')
            ->getQuery()
            ->getSingleScalarResult();
        /** @var int $torch */

        /** @var QueryBuilder $csa */
        $csa = self::searchBuilder($request, $joins);
        if (!in_array('g', $joins)) {
            $csa = $csa->leftJoin('u.groups', 'g');
        }
        $csa = $csa->select('COUNT(DISTINCT u.id)')
            ->andWhere('g.name LIKE \'%csa%\'')
            ->getQuery()
            ->getSingleScalarResult();
        /** @var int $csa */

        /** @var QueryBuilder $paid */
        $paid = self::searchBuilder($request, $joins);
        if (!in_array('g', $joins)) {
            $paid = $paid->leftJoin('u.groups', 'g');
        }
        $paid = $paid->select('COUNT(DISTINCT u.id)')
            ->andWhere('u.roles LIKE \'%s:9:"ROLE_PAID"%\'' . (!empty($paidStr) ? ' OR g.id IN (' . self::$paidStr . ')' : ''))
            ->getQuery()
            ->getSingleScalarResult();
        /** @var int $paid */

        // get the groups for use in dropdown
        $groups = $orm->getRepository('StudySauceBundle:Group')->findAll();

        // get the list of packs from the current list of users
        $resultPacks = call_user_func_array('array_merge', array_map(function (User $x) {return $x->getPacks();}, self::searchBuilder($request, $joins)
            ->select('u,up,p,a')
            ->leftJoin('u.userPacks', 'up')
            ->leftJoin('up.pack', 'p')
            ->leftJoin('u.authored', 'a')
            ->distinct()
            ->getQuery()
            ->getResult())); //array_unique());
        $packs = [];
        foreach($resultPacks as $p) {
            /** @var Pack $p */
            if(!in_array($p, $packs)) {
                $packs[] = $p;
            }
        }

        return $this->render('AdminBundle:Results:tab.html.php', [
            'users' => $users,
            'groups' => $groups,
            'parents' => $parents,
            'partners' => $partners,
            'advisers' => $advisers,
            'paid' => $paid,
            'students' => $students,
            'torch' => $torch,
            'csa' => $csa,
            'total' => $total,
            'packs' => $packs
        ]);
    }

    /**
     * @param $template
     * @param $class
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function templateAction($template, $class)
    {
        /** @var DelegatingEngine $tpl */
        $tpl = $this->container->get('templating');
        /** @var TimedPhpEngine $engine */
        $engine = $tpl->getEngine($template);
        $content = $engine->render($template, ['quiz' => new $class(), 'csrf_token' => '']);
        /** @var SlotsHelper $slots */
        //$slots = $engine->get('slots');
        return new Response($content);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function userAction(Request $request)
    {
        /** @var $userManager UserManager */
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserBy(['id' => intval($request->get('userId'))]);
        if(empty($user))
            throw new NotFoundHttpException();

        return $this->render('AdminBundle:Results:result.html.php', [
            'course1' => $user->getCourse1s()->first() ?: new Course1(),
            'course2' => $user->getCourse2s()->first() ?: new Course2(),
            'course3' => $user->getCourse3s()->first() ?: new Course3()
        ]);
    }
}