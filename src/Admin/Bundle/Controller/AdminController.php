<?php

namespace Admin\Bundle\Controller;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use StudySauce\Bundle\Controller\AccountController;
use StudySauce\Bundle\Controller\BuyController;
use StudySauce\Bundle\Controller\EmailsController as StudySauceEmails;
use StudySauce\Bundle\Entity\Coupon;
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Deadline;
use StudySauce\Bundle\Entity\Event;
use StudySauce\Bundle\Entity\Goal;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Schedule;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\Entity\UserPack;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class PartnerController
 * @package StudySauce\Bundle\Controller
 */
class AdminController extends Controller
{
    /** @var ClassMetadata[] $allTables */
    public static $allTables;
    /** @var ClassMetadata[] $allTableMetadata */
    public static $allTableMetadata;
    /** @var string[] $allTableClasses */
    public static $allTableClasses;
    public static $tables;

    public static $defaultSearch = ['tables' => ['ss_user', 'ss_group'], 'ss_user.deleted' => false, 'ss_group.deleted' => false, 'pack.status' => '!DELETED', 'card.deleted' => false];

    private function searchKeys($joinTable, $request) {
        $searchKeys = array_map(function ($f, $k) use ($joinTable) {
            $fields = array_map(function ($field) use ($joinTable) {
                return [$joinTable . '.' . $field, $field]; }, is_array($f) ? $f : [$f]);
            if (count($fields)) {
                $fields = call_user_func_array('array_merge', $fields);
            }
            if (is_array($f)) {
                $fields[] = $k;
            }
            return $fields;
        }, self::$tables[$joinTable], array_keys(self::$tables[$joinTable]));
        if (count($searchKeys) > 0) {
            $searchKeys = call_user_func_array('array_merge', $searchKeys);
        }
        $searches = array_filter(array_keys($request), function ($k) use ($request, $searchKeys) { return in_array($k, $searchKeys) && isset($request[$k]); });
        return $searches;
    }

    /**
     * @param QueryBuilder $qb
     * @param string $table
     * @param $tableName
     * @param array $request
     * @param array $joins
     * @return string
     */
    private function newSearchBuilder(QueryBuilder $qb, $table, $tableName, $request, &$joins = [])
    {
        /** @var QueryBuilder $qb $f */
        /** @var string $op */
        $where = '';
        if (!isset(self::$tables[$table])) {
            return '';
        }
        foreach(self::$tables[$table] as $k => $f) {
            if (!is_array($f)) {
                $f = [$f];
            }
            $joinWhere = '';
            $joinOp = 'OR';
            foreach($f as $field) {
                $search = null;

                // search for unions in original request only
                $op = 'AND';
                if (isset($request[$table . '.' . $field])) {
                    $search = $request[$table . '.' . $field];
                }
                else if (!empty($request[$field])) {
                    $search = $request[$field];
                }
                else if (is_array($f) && !empty($request[$k]))  {
                    $search = $request[$k];
                }
                else if (!empty($request[$table])) {
                    $search = $request[$table];
                }

                // do default searches like excluding deleted items
                else if (isset(self::$defaultSearch[$table . '.' . $field])) {
                    $search = self::$defaultSearch[$table . '.' . $field];
                }
                else if (!empty(self::$defaultSearch[$field])) {
                    $search = self::$defaultSearch[$field];
                }
                else if (is_array($f) && !empty(self::$defaultSearch[$k]))  {
                    $search = self::$defaultSearch[$k];
                }
                else if (!empty(self::$defaultSearch[$table])) {
                    $search = self::$defaultSearch[$table];
                }

                // general search is ORed together
                else if (!empty($request['search'])) {
                    $search = $request['search'];
                    $op = 'OR';
                }

                // only search joins on first connect
                if ($table == $tableName) {
                    $joinFields = explode('.', $field);
                    $joinTable = $table;
                    $joinName = $tableName;
                    foreach($joinFields as $jf) {
                        $associated = self::$allTables[$joinTable]->getAssociationMappings();
                        if (isset($associated[$jf])) {
                            $entity = $associated[$jf]['targetEntity'];
                            $ti = array_search($entity, self::$allTableClasses);
                            if ($ti !== false) {
                                $joinTable = self::$allTableMetadata[$ti]->table['name'];
                            }
                            else {
                                $meta = $this->get('doctrine')->getManager()->getMetadataFactory()->getMetadataFor($entity);
                                $joinTable = $meta->table['name'];
                            }
                            $newName = $joinName . '_' . preg_replace('[^a-z]', '_', $jf) . $joinTable;
                            if (!in_array($newName, $joins)) {
                                $joins[] = $newName;
                                $qb = $qb->leftJoin($joinName . '.' . $jf, $newName);
                            }
                            $joinName = $newName;
                        }
                        else {
                            // join failed, don't search any other tables this round
                            $joinName = null;
                            break;
                        }
                    }
                    // do one search on the last entity on the join, ie not searching intermediate tables like user_pack or ss_user_group
                    if (!empty($joinName) && isset(self::$tables[$joinTable])) {
                        $join = self::newSearchBuilder($qb, $joinTable, $joinName, $request, $joins);

                        if (count(self::searchKeys($joinTable, $request)) > 0) {
                            $joinOp = 'AND';
                        }
                        $joinWhere .= ($joinWhere == '' ? '' : (' OR ')) . (!empty($join) && empty($request['search']) ? ($joinName . ' IS NULL OR ') : '') . $join;
                    }
                }

                // only do a join if column name is specified
                if ($search === null || $search === '') {
                    continue;
                }

                $fields = self::$allTables[$table]->getFieldNames();
                if(in_array($field, $fields)) {
                    if (substr($search, 0, 1) == '!') {
                        if (is_numeric(substr($search, 1)) || is_bool(substr($search, 1))) {
                            $where .= ($where == '' ? '' : (' ' . $op . ' ')) . $tableName . '.' . $field . ' != :' . $tableName . '_field' . $field;
                            $qb = $qb->setParameter($tableName . '_field' . $field, substr($search, 1));
                        }
                        else {
                            $where .= ($where == '' ? '' : (' ' . $op . ' ')) . $tableName . '.' . $field . ' NOT LIKE :' . $tableName . '_field' . $field;
                            $qb = $qb->setParameter($tableName . '_field' . $field, '%' . substr($search, 1) . '%');
                        }
                    }
                    else if (is_numeric($search) || is_bool($search)) {
                        $where .= ($where == '' ? '' : (' ' . $op . ' ')) . $tableName . '.' . $field . ' = :' . $tableName . '_field' . $field;
                        $qb = $qb->setParameter($tableName . '_field' . $field, $search);
                    }
                    else {
                        $where .= ($where == '' ? '' : (' ' . $op . ' ')) . $tableName . '.' . $field . ' LIKE :' . $tableName . '_field' . $field;
                        $qb = $qb->setParameter($tableName . '_field' . $field, '%' . $search . '%');
                    }
                }

            }

            if (!empty($joinWhere)) {
                if (count(self::searchKeys($table, $request)) > 0) {
                    $joinOp = 'AND';
                }
                $where .= ($where == '' ? ' (' : (' ' . $joinOp . ' (')) . $joinWhere . ')';
            }
        }

        if(empty($where)) {
            return '';
        }
        return '(' . $where . ') ';
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('AdminBundle:Admin:tab.html.php');
    }

    public function resultsAction(Request $request) {
        set_time_limit(0);
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var $user User */
        $user = $this->getUser();

        if(!$user->hasRole('ROLE_ADMIN')) {
            throw new AccessDeniedHttpException();
        }

        self::$tables = [
            // TODO: simplify this maybe by specifying 'ss_user' => 'name' => 'authored,userPacks.pack'
            'ss_user' => ['id', 'name' => ['first','last','email'], 'groups', 'packs' => ['authored','userPacks.pack'], 'roles', 'actions' => ['deleted']],
            'ss_group' => ['id', 'name' => ['title','description'], 'users', 'packs' => ['packs','groupPacks'], 'roles', 'actions' => ['deleted']],
            'pack' => ['id', 'name' => ['title'], 'groups' => ['group','groups'], 'users' => ['user','userPacks.user'], 'status', 'actions'],
            'card' => ['id', 'name' => ['content','pack'], 'correct', 'answers', 'response', 'actions' => ['deleted']],
            // TODO: this really generalized template
            //'invite' => ['id', 'code', 'groups', 'users', 'properties', 'actions']
        ];

        self::$allTableClasses = $orm->getConfiguration()->getMetadataDriverImpl()->getAllClassNames();

        self::$allTableMetadata = array_map(function ($table) use ($orm) {return $orm->getMetadataFactory()->getMetadataFor($table);}, self::$allTableClasses);

        self::$allTables = array_combine(array_map(function (ClassMetadata $md) {return $md->getTableName();}, self::$allTableMetadata), self::$allTableMetadata);
        //$times = array_map(function($e) {
        /** @var User|Group $e */
        //    return $e->getCreated()->getTimestamp();
        //}, $entities);
        //array_multisort($times, SORT_NUMERIC, SORT_DESC, $entities);

        // default entities to show

        $request = array_merge($request->attributes->all(), $request->query->all());

        // pull out field searches
        $regex = '/(' . implode('|', array_map(function ($t, $table) {
                return implode('|', array_map(function ($f, $k) use ($table) {return $table . '\.' . (is_array($f) ? $k : $f);}, $t, array_keys($t)));
            }, self::$tables, array_keys(self::$tables))) . '):(.*)/i';
        if (isset($request['search']) && preg_match($regex, $request['search'], $matches)) {
            $request['search'] = str_replace($matches[0], '', $request['search']);
            $request[$matches[1]] = $matches[2];
        }

        if (!isset($request['tables'])) {
            $request['tables'] = self::$defaultSearch['tables'];
        }
        $vars['tables'] = array_intersect_key(self::$tables, array_flip($request['tables']));

        foreach(self::$tables as $table => $t) {
            if (!empty($request['tables']) && !in_array($table, $request['tables'])) {
                continue;
            }

            /** @var QueryBuilder $qb */
            $qb = $orm->getRepository(self::$allTables[$table]->name)->createQueryBuilder($table);
            $where = self::newSearchBuilder($qb, $table, $table, $request);
            if(!empty($where)) {
                $qb = $qb->where($where);
            }
            $totalQuery = $qb->select('COUNT(DISTINCT ' . $table . '.id)')
                ->getQuery();
            $total = $totalQuery->getSingleScalarResult();
            $vars[$table . '_total'] = $total;

            // max pagination to search count
            if(isset($request['page'])) {
                $page = $request['page'];
                if($page == 'last') {
                    $page = $total / 25;
                }
                $resultOffset = (min(max(1, ceil($total / 25)), max(1, intval($page))) - 1) * 25;
            }
            else {
                $resultOffset = 0;
            }

            // TODO: add sorting back in
            // figure out how to sort
            /*
            if(!empty($order = $request->get('order'))) {
                $field = explode(' ', $order)[0];
                $direction = explode(' ', $order)[1];
                if($direction != 'ASC' && $direction != 'DESC')
                    $direction = 'DESC';
                // no extra join information needed
                if($field == 'created' || $field == 'lastLogin' || $field == 'lastVisit' || $field == 'last') {
                    $users = $users->orderBy('u.' . $field, $direction);
                }
            }
            else {
                $users = $users->orderBy('u.lastVisit', 'DESC');
            }
            */
            $qb = $orm->getRepository(self::$allTables[$table]->name)->createQueryBuilder($table);
            $where = self::newSearchBuilder($qb, $table, $table, $request);
            if(!empty($where)) {
                $qb = $qb->where($where);
            }
            $query = $qb
                ->select($table)
                ->distinct(true)
                ->setFirstResult($resultOffset)
                ->setMaxResults(25)
                ->getQuery();
            $vars[$table] = $query->getResult();
        }

        $vars['allGroups'] = $orm->getRepository('StudySauceBundle:Group')->findAll();
        $vars['allTables'] = !empty($request['tables']) ? $request['tables'] : ['ss_user', 'ss_group', 'pack'];

        return $this->render('AdminBundle:Admin:results.html.php', $vars);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addUserAction(Request $request)
    {

        /** @var $user User */
        $user = $this->getUser();
        if (!$user->hasRole('ROLE_ADMIN')) {
            throw new AccessDeniedHttpException();
        }

        $account = new AccountController();
        $account->setContainer($this->container);
        $account->createAction($request, false, false);

        return $this->indexAction($request);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function saveUserAction(Request $request) {

        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var $user User */
        $user = $this->getUser();
        if(!$user->hasRole('ROLE_ADMIN')) {
            throw new AccessDeniedHttpException();
        }

        /** @var User $u */
        /** @var $userManager UserManager */
        $userManager = $this->get('fos_user.user_manager');
        if(!empty($request->get('users')))
        {
            foreach($request->get('users') as $user) {
                $u = $orm->getRepository('StudySauceBundle:User')->findOneBy(['id' => $user['userId']]);
                if(empty($u)) {
                    continue;
                }

                if(!empty($first = $user['firstName']))
                    $u->setFirst($first);
                if(!empty($last = $user['lastName']))
                    $u->setLast($last);
                if(!empty($email = $user['email'])) {
                    $u->setUsername($email);
                    $u->setEmail($email);
                    $userManager->updateCanonicalFields($u);
                }

                // add new groups
                $groups = $u->getGroups()->map(function (Group $g) {return $g->getId();})->toArray();
                $newGroups = explode(',', $user['groups']);
                // intersection with current groups is a removal, intersection with request is an addition
                foreach(array_diff($groups, $newGroups) as $i => $id) {
                    $u->removeGroup($u->getGroups()->filter(function (Group $g) use ($id) {return $g->getId() == $id;})->first());
                }
                foreach(array_diff($newGroups, $groups) as $i => $id) {
                    /** @var Group $g */
                    $g = $orm->getRepository('StudySauceBundle:Group')->findOneBy(['id' => $id]);
                    if(!empty($g))
                        $u->addGroup($g);
                }

                // add new roles
                $roles = $u->getRoles();
                $newRoles = explode(',', $user['roles']);
                // intersection with current groups is a removal, intersection with request is an addition
                foreach(array_diff($roles, $newRoles) as $i => $role) {
                    $u->removeRole($role);
                }
                foreach(array_diff($newRoles, $roles) as $i => $role) {
                    $u->addRole($role);
                }
                $userManager->updateUser($u);
            }
        }

        return $this->forward('AdminBundle:Admin:results', ['tables' => ['ss_user', 'ss_group']]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function saveGroupAction(Request $request) {

        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var $user User */
        $user = $this->getUser();
        if(!$user->hasRole('ROLE_ADMIN')) {
            throw new AccessDeniedHttpException();
        }

        /** @var Group $g */
        if(empty($request->get('groupId'))) {
            $g = new Group();
        }
        else {
            $g = $orm->getRepository('StudySauceBundle:Group')->findOneBy(['id' => $request->get('groupId')]);
        }

        if(!empty($name = $request->get('groupName')))
            $g->setName($name);
        $g->setDescription(!empty($request->get('description')) ? $request->get('description') : '');

        // add new roles
        $roles = $g->getRoles();
        $newRoles = explode(',', $request->get('roles'));
        // intersection with current groups is a removal, intersection with request is an addition
        foreach(array_diff($roles, $newRoles) as $i => $role) {
            $g->removeRole($role);
        }
        foreach(array_diff($newRoles, $roles) as $i => $role) {
            $g->addRole($role);
        }

        if(empty($g->getId()))
            $orm->persist($g);
        elseif($request->get('remove') == '1') {
            // remove group from users
            $invites = $orm->getRepository('StudySauceBundle:Invite')->findBy(['group' => $request->get('groupId')]);
            foreach($invites as $i => $in) {
                $orm->remove($in);
            }
            $coupons = $orm->getRepository('StudySauceBundle:Coupon')->findBy(['group' => $request->get('groupId')]);
            foreach($coupons as $i => $c) {
                /** @var Coupon $c */
                $c->setGroup(null);
                $orm->merge($c);
            }
            if($g->getUsers()->count() == 0) {
                $orm->remove($g);
            }
            else {
                $g->setDeleted(true);
            }
        }
        else
            $orm->merge($g);
        $orm->flush();

        return $this->forward('AdminBundle:Admin:results', ['tables' => ['ss_user', 'ss_group']]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function resetUserAction(Request $request) {

        /** @var $userManager UserManager */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var $user User */
        $user = $this->getUser();
        if(!$user->hasRole('ROLE_ADMIN')) {
            throw new AccessDeniedHttpException();
        }

        /** @var User $u */
        $u = $orm->getRepository('StudySauceBundle:User')->findOneBy(['id' => $request->get('userId')]);
        if(!empty($u)) {

            if ($u->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
                // TODO: error?
            }

            if (null === $u->getConfirmationToken()) {
                /** @var $tokenGenerator \FOS\UserBundle\Util\TokenGeneratorInterface */
                $tokenGenerator = $this->get('fos_user.util.token_generator');
                $u->setConfirmationToken($tokenGenerator->generateToken());
            }

            $emails = new StudySauceEmails();
            $emails->setContainer($this->container);
            $emails->resetPasswordAction($u);
            $u->setPasswordRequestedAt(new \DateTime());
            $userManager->updateUser($u);
        }

        return $this->indexAction($request);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cancelUserAction(Request $request) {

        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var $user User */
        $user = $this->getUser();
        if(!$user->hasRole('ROLE_ADMIN')) {
            throw new AccessDeniedHttpException();
        }

        /** @var User $u */
        $u = $orm->getRepository('StudySauceBundle:User')->findOneBy(['id' => $request->get('userId')]);
        if(!empty($u)) {
            $buy = new BuyController();;
            $buy->setContainer($this->container);
            $buy->cancelPaymentAction($u);
        }

        return $this->indexAction($request);
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeUserAction(Request $request) {

        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var $user User */
        $user = $this->getUser();
        if(!$user->hasRole('ROLE_ADMIN')) {
            throw new AccessDeniedHttpException();
        }

        /** @var User $u */
        $u = $orm->getRepository('StudySauceBundle:User')->findOneBy(['id' => $request->get('userId')]);
        if(!empty($u)) {
            // remove all entities attached
            $orm->getRepository('StudySauceBundle:Visit')->createQueryBuilder('v')
                ->delete()
                ->andWhere('v.user = :uid')
                ->setParameter(':uid', $u)
                ->getQuery()->execute();
            $orm->getRepository('StudySauceBundle:Response')->createQueryBuilder('r')
                ->delete()
                ->andWhere('r.user = :uid')
                ->setParameter(':uid', $u)
                ->getQuery()->execute();
            foreach($u->getFiles()->toArray() as $i => $f) {
                $u->removeFile($f);
                $orm->remove($f);
            }
            foreach($u->getGroups()->toArray() as $i => $gr) {
                $u->removeGroup($gr);
            }
            foreach($u->getInvites()->toArray() as $i => $gri) {
                $u->removeInvite($gri);
                $orm->remove($gri);
            }
            foreach($u->getPayments()->toArray() as $i => $pay) {
                $u->removePayment($pay);
                $orm->remove($pay);
            }
            foreach($u->getUserPacks()->toArray() as $i => $s) {
                /** @var UserPack $s */
                $u->removeUserPack($s);
                $orm->remove($s);
            }
            $orm->flush();
            foreach($u->getInvitees()->toArray() as $i => $ig) {
                $u->removeInvitee($ig);
                $orm->remove($ig);
            }
            $orm->remove($u);
            $orm->flush();
        }

        return $this->indexAction($request);
    }
}
