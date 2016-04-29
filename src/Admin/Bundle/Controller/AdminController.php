<?php

namespace Admin\Bundle\Controller;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Parameter;
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
use StudySauce\Bundle\Entity\File;
use StudySauce\Bundle\Entity\Goal;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Invite;
use StudySauce\Bundle\Entity\Schedule;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\Entity\UserPack;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Acl\Dbal\AclProvider;
use Symfony\Component\Security\Acl\Dbal\MutableAclProvider;
use Symfony\Component\Security\Acl\Domain\Acl;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;

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
    /** @var array $defaultTables A list of all available fields, firewall */
    public static $defaultTables = [ // database table and field firewall
        // TODO: simplify this maybe by specifying 'ss_user' => 'name' => 'authored,userPacks.pack'
        'ss_user' => ['id' => ['lastVisit', 'created', 'id'], 'name' => ['first','last','email'], 'groups', 'packs' => ['authored','userPacks.pack'], 'roles', 'actions' => ['deleted']],
        'ss_group' => ['id' => ['created', 'id'], 'name' => ['name','userCountStr','descriptionStr'], 'parent', 'invites', 'packs' => ['packs','groupPacks'], 'actions' => ['deleted']],
        'pack' => ['id' => ['modified', 'created', 'id'], 'name' => ['title','userCountStr','cardCountStr'], 'status', ['group','groups', 'user','userPacks.user'], 'properties', 'actions'],
        'card' => ['id', 'name' => ['content','pack'], 'correct', 'actions' => ['deleted']],
        'invite' => ['id', 'name'=> ['code', 'email', 'created'], 'actions' => ['deleted']],
        'user_pack' => ['user', 'pack', 'removed']
        // TODO: this really generalized template
        //'invite' => ['id', 'code', 'groups', 'users', 'properties', 'actions']
    ];

    public static $defaultSearch = ['tables' => ['ss_user', 'ss_group'], 'user_pack-removed' => false, 'ss_user-enabled' => true, 'ss_group-deleted' => false, 'parent-ss_group-deleted' => null, 'pack-status' => '!DELETED', 'card-deleted' => false];


    private static function getSearchValue($field, $k, $f, $table, $request) {
        // search for unions in original request only
        if (isset($request[$table . '-' . $field])) {
            return [$table . '-' . $field, $request[$table . '-' . $field]];
        }
        else if (!empty($request[$field])) {
            return [$field, $request[$field]];
        }
        else if (is_array($f) && !empty($request[$k]))  {
            return [$k, $request[$k]];
        }
        else if (!empty($request[$table])) {
            return [$table, $request[$table]];
        }
        else if (!empty($request['search'])) {
            return ['search', $request['search']];
        }
        return [null, null];
    }

    private static function joinBuilder(QueryBuilder $qb, $joinTable, $joinName, $field, $request, &$joins = [])
    {
        // TODO: add userPacks-removed = false search field
        $result = '';
        $joinFields = explode('.', $field);
        foreach($joinFields as $jf) {
            $associated = self::$allTables[$joinTable]->getAssociationMappings();
            if (isset($associated[$jf])) {
                $entity = $associated[$jf]['targetEntity'];
                $ti = array_search($entity, self::$allTableClasses);
                if ($ti !== false) {
                    $joinTable = self::$allTableMetadata[$ti]->table['name'];
                }
                else {
                    continue;
                }
                $newName = $joinName . '_' . preg_replace('[^a-z]', '_', $jf) . $joinTable;
                if (!in_array($newName, $joins)) {
                    $joins[] = $newName;
                    $qb = $qb->leftJoin($joinName . '.' . $jf, $newName);
                    if(in_array($joinTable, array_keys($request['tables']))) {
                        $result .= (!empty($result) ? ' AND ' : '') . self::searchBuilder($qb, $joinTable, $newName, $request, $joins);
                    }
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
        if (!empty($joinName) && isset($request['tables'][$joinTable])) {
            $result .= (!empty($result) ? ' AND ' : '') . self::searchBuilder($qb, $joinTable, $joinName, $request, $joins);
        }
        return $result;
    }

    /**
     * @param QueryBuilder $qb
     * @param string $table
     * @param $tableName
     * @param array $request
     * @param array $joins
     * @param bool $isDefault
     * @return string
     */
    private static function searchBuilder(QueryBuilder $qb, $table, $tableName, $request, &$joins = [], $isDefault = false)
    {
        /** @var QueryBuilder $qb $f */
        /** @var string $op */
        $where = [];
        if (!isset($request['tables'][$table])) {
            return '';
        }
        else {
            $searchTables = $request['tables'][$table];
        }
        $allFields = self::getAllFieldNames([$table => self::$defaultTables[$table]]);
        foreach($searchTables as $k => $f) {
            if (!is_array($f)) {
                $f = [$f];
            }
            foreach($f as $field) {
                // Skip restricted fields, all available fields are listed above
                if(!in_array($field, $allFields)) {
                    continue;
                }
                $search = null;

                // by default, columns searching on the same term are ORed together
                list($searchField, $search) = self::getSearchValue($field, $k, $f, $table, $request);
                if ($isDefault) {
                    $searchField .= '_default';
                }
                else {

                    // only search joins on first connection
                    if ($table == $tableName) {
                        // remove table prefix from matching field
                        $joinRequest = [];
                        foreach($request as $j => $r) {
                            $joinRequest[$j] = $r;
                            if(substr($j, 0, strlen($field)+1) == $field . '-' && strpos($j, '-', strlen($field)+1) !== false) {
                                $joinRequest[substr($j, strlen($field)+1)] = $r;
                            }
                        }
                        $join = self::joinBuilder($qb, $table, $tableName, $field, $joinRequest, $joins);
                        if (!empty($join)) {
                            $where['join'] = (empty($where['join']) ? '' : ($where['join'] . ' OR ')) . $join;
                        }
                    }
                }

                // only do a join if column name is specified
                if ($search === null || $search === '') {
                    continue;
                }

                $searchField = preg_replace('/[^a-z0-9_]/i', '_', $searchField);
                $fields = self::$allTables[$table]->getFieldNames();
                if(in_array($field, $fields)) {
                    list($searchWhere, $searchKey, $searchValue) = self::getWhereValue($search, $searchField, $field, $tableName);
                    if (!empty($searchWhere)) {
                        $where[$searchField] = (empty($where[$searchField]) ? '' : ($where[$searchField] . ' OR ')) . $searchWhere;
                        if (!empty($searchKey) && $qb->getParameters()->filter(function (Parameter $p) use ($searchKey) {return $p->getName() == $searchKey;})->count() == 0) {
                            $qb = $qb->setParameter($searchKey, $searchValue);
                        }
                    }
                }
            }
        }

        if(empty($where)) {
            return '';
        }
        // meet each criteria using AND
        return '(' . implode(' AND ', $where) . ') ';
    }

    private static function getWhereValue($search, $searchField, $field, $tableName) {
        if (substr($search, 0, 1) == '!') {
            $search = substr($search, 1);
            if (is_bool($search) || $search === 'false' || $search === 'true') {
                $boolval = ( is_string($search) ? filter_var($search, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : boolval($search) );
                return [$tableName . '.' . $field . ' != :' . $searchField . '_bool_not', $searchField . '_bool_not', $boolval];
            }
            else if (is_numeric($search)) {
                return [$tableName . '.' . $field . ' != :' . $searchField . '_int_not', $searchField . '_int_not', intval($search)];
            }
            else if ($search == 'NULL') {
                return [$tableName . '.' . $field . ' IS NOT NULL', null, null];
            }
            else {
                return [$tableName . '.' . $field . ' NOT LIKE :' . $searchField . '_string_not', $searchField . '_string_not', '%' . $search . '%'];
            }
        }
        else {
            if (is_bool($search) || $search === 'false' || $search === 'true') {
                $boolval = ( is_string($search) ? filter_var($search, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : boolval($search) );
                return [$tableName . '.' . $field . ' = :' . $searchField . '_bool', $searchField . '_bool', $boolval];
            }
            else if (is_numeric($search)) {
                return [$tableName . '.' . $field . ' = :' . $searchField . '_int', $searchField . '_int', intval($search)];
            }
            else if ($search == 'NULL') {
                return [$tableName . '.' . $field . ' IS NULL', null, null];
            }
            else {
                return [$tableName . '.' . $field . ' LIKE :' . $searchField . '_string', $searchField . '_string', '%' . $search . '%'];
            }
        }
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('AdminBundle:Admin:tab.html.php');
    }

    public static function getAllFieldNames($tables) {
        $allFields = array_map(function ($t, $table) {
            $fields = array_map(function ($f, $k) use ($table) {
                return is_array($f)
                    ? array_merge([$k], array_map(function ($field) use ($table) {return $table . '-' . $field;}, $f), array_map(function ($field) {return $field;}, $f))
                    : [$f, $table . '-' . $f];
            }, $t, array_keys($t));
            if (count($fields)) {
                return call_user_func_array('array_merge', $fields);
            }
            return [];
        }, $tables, array_keys($tables));
        if (count($allFields) > 0) {
            $allFields = call_user_func_array('array_merge', $allFields);
        }
        $allFields = array_merge($allFields, array_keys($tables));
        $allFields = array_unique($allFields);
        return $allFields;
    }

    public function resultsAction(Request $request) {
        set_time_limit(0);
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var $user User */
        $user = $this->getUser();

        if(empty($user) || !$user->hasRole('ROLE_ADMIN')) {
            throw new AccessDeniedHttpException();
        }

        self::$allTableClasses = $orm->getConfiguration()->getMetadataDriverImpl()->getAllClassNames();

        self::$allTableMetadata = array_map(function ($table) use ($orm) {return $orm->getMetadataFactory()->getMetadataFor($table);}, self::$allTableClasses);

        self::$allTables = array_combine(array_map(function (ClassMetadata $md) {return $md->getTableName();}, self::$allTableMetadata), self::$allTableMetadata);

        // pull out field searches
        $allFields = self::getAllFieldNames(self::$defaultTables);
        //$times = array_map(function($e) {
        /** @var User|Group $e */
        //    return $e->getCreated()->getTimestamp();
        //}, $entities);
        //array_multisort($times, SORT_NUMERIC, SORT_DESC, $entities);

        // default entities to show
        if(!empty($key = $request->get('requestKey'))) {
            $cacheRequest = $this->get('cache')->fetch($key);
            if(empty($cacheRequest)) {
                throw new NotFoundHttpException('Could not find request key');
            }
            else {
                $searchRequest = unserialize($cacheRequest);
            }
        }
        else {
            $searchRequest = array_merge($request->attributes->all(), $request->query->all());

            // setup the tables we are searching
            if (!isset($searchRequest['tables'])) {
                $searchRequest['tables'] = [];
            }
            // convert table list to table => field list format
            $tblList = [];
            foreach($searchRequest['tables'] as $table => $t) {
                if(!is_array($t)) {
                    $table = $t;
                    $t = self::$defaultTables[$t];
                }
                $tblList[$table] = $t;
            }
            $searchRequest['tables'] = $tblList;

            // TODO: fix this to do all occurrences and table names and other formats of searches specified above
            $regex = '/\s(' . implode('|', $allFields) . '):(.*)\s/i';
            if (isset($searchRequest['search']) && preg_match_all($regex, ' ' . $searchRequest['search'] . ' ', $matches)) {
                foreach($matches[0] as $i => $m) {
                    $searchRequest['search'] = str_replace(trim($m), '', $searchRequest['search']);
                    $searchRequest[$matches[1][$i]] = $matches[2][$i];
                }
            }
        }

        $vars['search'] = '';
        foreach($searchRequest['tables'] as $table => $t) {
            $vars['tables'][$table] = $t;

            $resultCount = isset($searchRequest['count-' . $table]) ? intval($searchRequest['count-' . $table]) : 25;
            // for templating purposes only
            if($resultCount == -1 || isset($searchRequest['new']) && ($searchRequest['new'] === true || is_array($searchRequest['new']) && in_array($table, $searchRequest['new']))) {
                $vars[$table] = [];
                $vars[$table . '_total'] = 0;
                continue;
            }

            /** @var QueryBuilder $qb */
            $qb = $orm->getRepository(self::$allTables[$table]->name)->createQueryBuilder($table);
            $joins = [];
            $where = self::searchBuilder($qb, $table, $table, $searchRequest, $joins);
            $defaultWhere = self::searchBuilder($qb, $table, $table, array_diff_key(self::$defaultSearch, $searchRequest) + ['tables' => [$table => self::$defaultTables[$table]]], $joins, true);
            if(!empty($where)) {
                $qb = $qb->where($where);
            }
            if(!empty($defaultWhere)) {
                $qb = $qb->andWhere($defaultWhere);
            }

            $totalQuery = clone $qb;
            $query = $totalQuery->select('COUNT(DISTINCT(' . $table . '.' . self::$allTables[$table]->identifier[0] . '))')
                ->getQuery();
            $total = $query->getSingleScalarResult();
            $vars[$table . '_total'] = $total;

            // max pagination to search count
            if(!empty($resultCount) && isset($searchRequest['page-' . $table])) {
                $page = $searchRequest['page-' . $table];
                if($page == 'last') {
                    $page = $total / $resultCount;
                }
                $resultOffset = (min(max(1, ceil($total / $resultCount)), max(1, intval($page))) - 1) * $resultCount;
            }
            else {
                $resultOffset = 0;
            }

            // TODO: add sorting back in
            // figure out how to sort
            /*
            if(!empty($order = $searchRequest->get('order'))) {
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

            $qb = $qb
                ->select($table)
                ->distinct(true);
            if (isset($t['id']) && is_array($t['id']) && !empty($t['id']) && in_array($t, $allFields)) {
                $qb = $qb->orderBy($table . '.' . $t['id'][0], 'DESC');
            }
            $query = $qb->setFirstResult($resultOffset);
            if($resultCount > 0) {
                $query = $query->setMaxResults($resultCount);
            }

            $vars[$table] = $query->getQuery()->getResult();
        }

        $vars['allGroups'] = $orm->getRepository('StudySauceBundle:Group')->findAll();
        $serialized = serialize($searchRequest);
        $searchRequest['requestKey'] = md5($serialized);
        $this->get('cache')->save($searchRequest['requestKey'], $serialized);
        $vars['searchRequest'] = $searchRequest;

        // if request is json, merge the table fields plus a list of all the groups the user has access to
        if(in_array('application/json', $request->getAcceptableContentTypes()) || $request->get('dataType') == 'json') {
            // convert db entity to flat object
            foreach(array_merge(array_keys($searchRequest['tables']), ['allGroups']) as $table) {
                if (!isset($vars[$table])) {
                    continue;
                }
                $tableName = $table;
                if ($table == 'allGroups') {
                    $tableName = 'ss_group';
                }
                $allowedFields = self::getAllFieldNames([$tableName => self::$defaultTables[$tableName]]);
                $fields = array_intersect(self::getAllFieldNames([$tableName => $table == 'allGroups'
                    ? self::$defaultTables['ss_group']
                    : $searchRequest['tables'][$tableName]]), $allowedFields);
                $vars[$table] = array_map(function ($e) use ($fields) {
                    $obj = [];
                    foreach($fields as $f) {
                        if (!method_exists($e, 'get' . ucfirst($f))) {
                            continue;
                        }
                        if (is_object($e->{'get' . ucfirst($f)}()) && $e->{'get' . ucfirst($f)}() instanceof \DateTime) {
                            $obj[$f] = $e->{'get' . ucfirst($f)}()->format('r');
                        }
                        else {
                            $obj[$f] = $e->{'get' . ucfirst($f)}();
                        }
                    }
                    return $obj;
                }, $vars[$table]);
            }
            return new JsonResponse($vars);
        }

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
        /** @var $userManager UserManager */
        $userManager = $this->get('fos_user.user_manager');

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

        if(!empty($request->get('upload'))) {
            $logo = $user->getFiles()->filter(function (File $f) use ($request) {
                return $f->getUrl() == $request->get('upload');
            })->first();
            $g->setLogo(empty($logo) ? null : $logo);
        }

        if(!empty($request->get('parent'))) {
            /** @var Group $parent */
            $parent = $orm->getRepository('StudySauceBundle:Group')->findOneBy(['id' => $request->get('parent')]);
            if (!empty($parent) && $parent == $g) {
                $parent->removeSubgroup($g);
                $g->setParent(null);
            }
            else if(!empty($parent)) {
                $g->setParent($parent);
                $parent->addSubgroup($g);
            }
        }

        if(!empty($name = $request->get('groupName'))) {
            $g->setName(trim($name));
        }

        if($request->get('description') !== false) {
            $g->setDescription(!empty($request->get('description')) ? $request->get('description') : '');
        }

        // add new roles
        if(!empty($request->get('roles'))) {
            $roles = $g->getRoles();
            $newRoles = explode(',', $request->get('roles'));
            // intersection with current groups is a removal, intersection with request is an addition
            foreach (array_diff($roles, $newRoles) as $i => $role) {
                $g->removeRole($role);
            }
            foreach (array_diff($newRoles, $roles) as $i => $role) {
                $g->addRole($role);
            }
        }

        // do pack add/remove, not group remove
        if(!empty($request->get('packId')) && !empty($g->getId())) {
            $this->forward('StudySauceBundle:Packs:create', ['id' => $request->get('packId'), 'groups' => $request->get('groups'), 'users' => $request->get('users'), 'publish' => $request->get('publish')]);
        }
        else if(empty($g->getId())) {
            $orm->persist($g);
        }
        else if($request->get('remove') == 'true') {
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
                $g->setName($g->getName() . '-Deleted On ' . time());
                $g->setDeleted(true);
            }
            //foreach($g->getUsers()->toArray() as $i => $u) {
            //    /** @var User $u */
            //    $u->removeGroup($g);
            //    $g->removeUser($u);
            //    $userManager->updateUser($u, false);
            //}
            $orm->flush();
            return $this->redirect($this->generateUrl('groups'));
        }
        else {
            $orm->merge($g);
        }
        $orm->flush();

        if(!empty($request->get('invite'))) {
            $invites = $orm->getRepository('StudySauceBundle:Invite')->findBy(['code' => $request->get('invite')]);
            if (count($invites) == 0) {
                $newInvite = new Invite();
                $newInvite->setCode($request->get('invite'));
            }
            else {
                $newInvite = $invites[0];
            }
            $newInvite->setUser($user);
            $newInvite->setFirst('');
            $newInvite->setLast('');
            $newInvite->setEmail('');
            $newInvite->setGroup($g);
            $newInvite->setActivated(true);
            $g->addInvite($newInvite);
            if (count($invites) == 0) {
                $orm->persist($newInvite);
            }
            else {
                $orm->merge($newInvite);
            }
            $orm->flush();
        }

        return $this->forward('AdminBundle:Admin:results', [
            'count-pack' => 0,
            'count-ss_group' => 1,
            'ss_group-deleted' => $g->getDeleted(),
            'edit' => false,
            'read-only' => ['ss_group'],
            'new' => false,
            'ss_group-id' => $g->getId(),
            'tables' => [
                'ss_group' => ['id' => ['created', 'id'], 'name' => ['name', 'description'], 'parent' => [''], 'invite' => ['invites'], 'subgroup' => [], 'actions' => ['deleted']],
                'pack' => ['title', 'counts', 'members' => ['groups'], 'actionsGroup' => ['status'] /* search field but don't display a template */]],
            'headers' => ['ss_group' => 'groupGroups', 'pack' => 'groupPacks'],
            'footers' => ['ss_group' => 'groupGroups'],
            'requestKey' => null
        ]);
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

/**
 * SQL Formatter is a collection of utilities for debugging SQL queries.
 * It includes methods for formatting, syntax highlighting, removing comments, etc.
 *
 * @package    SqlFormatter
 * @author     Jeremy Dorn <jeremy@jeremydorn.com>
 * @author     Florin Patan <florinpatan@gmail.com>
 * @copyright  2013 Jeremy Dorn
 * @license    http://opensource.org/licenses/MIT
 * @link       http://github.com/jdorn/sql-formatter
 * @version    1.2.18
 */
class SqlFormatter
{
    // Constants for token types
    const TOKEN_TYPE_WHITESPACE = 0;
    const TOKEN_TYPE_WORD = 1;
    const TOKEN_TYPE_QUOTE = 2;
    const TOKEN_TYPE_BACKTICK_QUOTE = 3;
    const TOKEN_TYPE_RESERVED = 4;
    const TOKEN_TYPE_RESERVED_TOPLEVEL = 5;
    const TOKEN_TYPE_RESERVED_NEWLINE = 6;
    const TOKEN_TYPE_BOUNDARY = 7;
    const TOKEN_TYPE_COMMENT = 8;
    const TOKEN_TYPE_BLOCK_COMMENT = 9;
    const TOKEN_TYPE_NUMBER = 10;
    const TOKEN_TYPE_ERROR = 11;
    const TOKEN_TYPE_VARIABLE = 12;

    // Constants for different components of a token
    const TOKEN_TYPE = 0;
    const TOKEN_VALUE = 1;

    // Reserved words (for syntax highlighting)
    protected static $reserved = array(
        'ACCESSIBLE', 'ACTION', 'AGAINST', 'AGGREGATE', 'ALGORITHM', 'ALL', 'ALTER', 'ANALYSE', 'ANALYZE', 'AS', 'ASC',
        'AUTOCOMMIT', 'AUTO_INCREMENT', 'BACKUP', 'BEGIN', 'BETWEEN', 'BINLOG', 'BOTH', 'CASCADE', 'CASE', 'CHANGE', 'CHANGED', 'CHARACTER SET',
        'CHARSET', 'CHECK', 'CHECKSUM', 'COLLATE', 'COLLATION', 'COLUMN', 'COLUMNS', 'COMMENT', 'COMMIT', 'COMMITTED', 'COMPRESSED', 'CONCURRENT',
        'CONSTRAINT', 'CONTAINS', 'CONVERT', 'CREATE', 'CROSS', 'CURRENT_TIMESTAMP', 'DATABASE', 'DATABASES', 'DAY', 'DAY_HOUR', 'DAY_MINUTE',
        'DAY_SECOND', 'DEFAULT', 'DEFINER', 'DELAYED', 'DELETE', 'DESC', 'DESCRIBE', 'DETERMINISTIC', 'DISTINCT', 'DISTINCTROW', 'DIV',
        'DO', 'DUMPFILE', 'DUPLICATE', 'DYNAMIC', 'ELSE', 'ENCLOSED', 'END', 'ENGINE', 'ENGINE_TYPE', 'ENGINES', 'ESCAPE', 'ESCAPED', 'EVENTS', 'EXEC',
        'EXECUTE', 'EXISTS', 'EXPLAIN', 'EXTENDED', 'FAST', 'FIELDS', 'FILE', 'FIRST', 'FIXED', 'FLUSH', 'FOR', 'FORCE', 'FOREIGN', 'FULL', 'FULLTEXT',
        'FUNCTION', 'GLOBAL', 'GRANT', 'GRANTS', 'GROUP_CONCAT', 'HEAP', 'HIGH_PRIORITY', 'HOSTS', 'HOUR', 'HOUR_MINUTE',
        'HOUR_SECOND', 'IDENTIFIED', 'IF', 'IFNULL', 'IGNORE', 'IN', 'INDEX', 'INDEXES', 'INFILE', 'INSERT', 'INSERT_ID', 'INSERT_METHOD', 'INTERVAL',
        'INTO', 'INVOKER', 'IS', 'ISOLATION', 'KEY', 'KEYS', 'KILL', 'LAST_INSERT_ID', 'LEADING', 'LEVEL', 'LIKE', 'LINEAR',
        'LINES', 'LOAD', 'LOCAL', 'LOCK', 'LOCKS', 'LOGS', 'LOW_PRIORITY', 'MARIA', 'MASTER', 'MASTER_CONNECT_RETRY', 'MASTER_HOST', 'MASTER_LOG_FILE',
        'MATCH','MAX_CONNECTIONS_PER_HOUR', 'MAX_QUERIES_PER_HOUR', 'MAX_ROWS', 'MAX_UPDATES_PER_HOUR', 'MAX_USER_CONNECTIONS',
        'MEDIUM', 'MERGE', 'MINUTE', 'MINUTE_SECOND', 'MIN_ROWS', 'MODE', 'MODIFY',
        'MONTH', 'MRG_MYISAM', 'MYISAM', 'NAMES', 'NATURAL', 'NOT', 'NOW()','NULL', 'OFFSET', 'ON', 'OPEN', 'OPTIMIZE', 'OPTION', 'OPTIONALLY',
        'ON UPDATE', 'ON DELETE', 'OUTFILE', 'PACK_KEYS', 'PAGE', 'PARTIAL', 'PARTITION', 'PARTITIONS', 'PASSWORD', 'PRIMARY', 'PRIVILEGES', 'PROCEDURE',
        'PROCESS', 'PROCESSLIST', 'PURGE', 'QUICK', 'RANGE', 'RAID0', 'RAID_CHUNKS', 'RAID_CHUNKSIZE','RAID_TYPE', 'READ', 'READ_ONLY',
        'READ_WRITE', 'REFERENCES', 'REGEXP', 'RELOAD', 'RENAME', 'REPAIR', 'REPEATABLE', 'REPLACE', 'REPLICATION', 'RESET', 'RESTORE', 'RESTRICT',
        'RETURN', 'RETURNS', 'REVOKE', 'RLIKE', 'ROLLBACK', 'ROW', 'ROWS', 'ROW_FORMAT', 'SECOND', 'SECURITY', 'SEPARATOR',
        'SERIALIZABLE', 'SESSION', 'SHARE', 'SHOW', 'SHUTDOWN', 'SLAVE', 'SONAME', 'SOUNDS', 'SQL',  'SQL_AUTO_IS_NULL', 'SQL_BIG_RESULT',
        'SQL_BIG_SELECTS', 'SQL_BIG_TABLES', 'SQL_BUFFER_RESULT', 'SQL_CALC_FOUND_ROWS', 'SQL_LOG_BIN', 'SQL_LOG_OFF', 'SQL_LOG_UPDATE',
        'SQL_LOW_PRIORITY_UPDATES', 'SQL_MAX_JOIN_SIZE', 'SQL_QUOTE_SHOW_CREATE', 'SQL_SAFE_UPDATES', 'SQL_SELECT_LIMIT', 'SQL_SLAVE_SKIP_COUNTER',
        'SQL_SMALL_RESULT', 'SQL_WARNINGS', 'SQL_CACHE', 'SQL_NO_CACHE', 'START', 'STARTING', 'STATUS', 'STOP', 'STORAGE',
        'STRAIGHT_JOIN', 'STRING', 'STRIPED', 'SUPER', 'TABLE', 'TABLES', 'TEMPORARY', 'TERMINATED', 'THEN', 'TO', 'TRAILING', 'TRANSACTIONAL', 'TRUE',
        'TRUNCATE', 'TYPE', 'TYPES', 'UNCOMMITTED', 'UNIQUE', 'UNLOCK', 'UNSIGNED', 'USAGE', 'USE', 'USING', 'VARIABLES',
        'VIEW', 'WHEN', 'WITH', 'WORK', 'WRITE', 'YEAR_MONTH'
    );

    // For SQL formatting
    // These keywords will all be on their own line
    protected static $reserved_toplevel = array(
        'SELECT', 'FROM', 'WHERE', 'SET', 'ORDER BY', 'GROUP BY', 'LIMIT', 'DROP',
        'VALUES', 'UPDATE', 'HAVING', 'ADD', 'AFTER', 'ALTER TABLE', 'DELETE FROM', 'UNION ALL', 'UNION', 'EXCEPT', 'INTERSECT'
    );

    protected static $reserved_newline = array(
        'LEFT OUTER JOIN', 'RIGHT OUTER JOIN', 'LEFT JOIN', 'RIGHT JOIN', 'OUTER JOIN', 'INNER JOIN', 'JOIN', 'XOR', 'OR', 'AND'
    );

    protected static $functions = array (
        'ABS', 'ACOS', 'ADDDATE', 'ADDTIME', 'AES_DECRYPT', 'AES_ENCRYPT', 'AREA', 'ASBINARY', 'ASCII', 'ASIN', 'ASTEXT', 'ATAN', 'ATAN2',
        'AVG', 'BDMPOLYFROMTEXT',  'BDMPOLYFROMWKB', 'BDPOLYFROMTEXT', 'BDPOLYFROMWKB', 'BENCHMARK', 'BIN', 'BIT_AND', 'BIT_COUNT', 'BIT_LENGTH',
        'BIT_OR', 'BIT_XOR', 'BOUNDARY',  'BUFFER',  'CAST', 'CEIL', 'CEILING', 'CENTROID',  'CHAR', 'CHARACTER_LENGTH', 'CHARSET', 'CHAR_LENGTH',
        'COALESCE', 'COERCIBILITY', 'COLLATION',  'COMPRESS', 'CONCAT', 'CONCAT_WS', 'CONNECTION_ID', 'CONTAINS', 'CONV', 'CONVERT', 'CONVERT_TZ',
        'CONVEXHULL',  'COS', 'COT', 'COUNT', 'CRC32', 'CROSSES', 'CURDATE', 'CURRENT_DATE', 'CURRENT_TIME', 'CURRENT_TIMESTAMP', 'CURRENT_USER',
        'CURTIME', 'DATABASE', 'DATE', 'DATEDIFF', 'DATE_ADD', 'DATE_DIFF', 'DATE_FORMAT', 'DATE_SUB', 'DAY', 'DAYNAME', 'DAYOFMONTH', 'DAYOFWEEK',
        'DAYOFYEAR', 'DECODE', 'DEFAULT', 'DEGREES', 'DES_DECRYPT', 'DES_ENCRYPT', 'DIFFERENCE', 'DIMENSION', 'DISJOINT', 'DISTANCE', 'ELT', 'ENCODE',
        'ENCRYPT', 'ENDPOINT', 'ENVELOPE', 'EQUALS', 'EXP', 'EXPORT_SET', 'EXTERIORRING', 'EXTRACT', 'EXTRACTVALUE', 'FIELD', 'FIND_IN_SET', 'FLOOR',
        'FORMAT', 'FOUND_ROWS', 'FROM_DAYS', 'FROM_UNIXTIME', 'GEOMCOLLFROMTEXT', 'GEOMCOLLFROMWKB', 'GEOMETRYCOLLECTION', 'GEOMETRYCOLLECTIONFROMTEXT',
        'GEOMETRYCOLLECTIONFROMWKB', 'GEOMETRYFROMTEXT', 'GEOMETRYFROMWKB', 'GEOMETRYN', 'GEOMETRYTYPE', 'GEOMFROMTEXT', 'GEOMFROMWKB', 'GET_FORMAT',
        'GET_LOCK', 'GLENGTH', 'GREATEST', 'GROUP_CONCAT', 'GROUP_UNIQUE_USERS', 'HEX', 'HOUR', 'IF', 'IFNULL', 'INET_ATON', 'INET_NTOA', 'INSERT', 'INSTR',
        'INTERIORRINGN', 'INTERSECTION', 'INTERSECTS',  'INTERVAL', 'ISCLOSED', 'ISEMPTY', 'ISNULL', 'ISRING', 'ISSIMPLE', 'IS_FREE_LOCK', 'IS_USED_LOCK',
        'LAST_DAY', 'LAST_INSERT_ID', 'LCASE', 'LEAST', 'LEFT', 'LENGTH', 'LINEFROMTEXT', 'LINEFROMWKB', 'LINESTRING', 'LINESTRINGFROMTEXT', 'LINESTRINGFROMWKB',
        'LN', 'LOAD_FILE', 'LOCALTIME', 'LOCALTIMESTAMP', 'LOCATE', 'LOG', 'LOG10', 'LOG2', 'LOWER', 'LPAD', 'LTRIM', 'MAKEDATE', 'MAKETIME', 'MAKE_SET',
        'MASTER_POS_WAIT', 'MAX', 'MBRCONTAINS', 'MBRDISJOINT', 'MBREQUAL', 'MBRINTERSECTS', 'MBROVERLAPS', 'MBRTOUCHES', 'MBRWITHIN', 'MD5', 'MICROSECOND',
        'MID', 'MIN', 'MINUTE', 'MLINEFROMTEXT', 'MLINEFROMWKB', 'MOD', 'MONTH', 'MONTHNAME', 'MPOINTFROMTEXT', 'MPOINTFROMWKB', 'MPOLYFROMTEXT', 'MPOLYFROMWKB',
        'MULTILINESTRING', 'MULTILINESTRINGFROMTEXT', 'MULTILINESTRINGFROMWKB', 'MULTIPOINT',  'MULTIPOINTFROMTEXT', 'MULTIPOINTFROMWKB', 'MULTIPOLYGON',
        'MULTIPOLYGONFROMTEXT', 'MULTIPOLYGONFROMWKB', 'NAME_CONST', 'NULLIF', 'NUMGEOMETRIES', 'NUMINTERIORRINGS',  'NUMPOINTS', 'OCT', 'OCTET_LENGTH',
        'OLD_PASSWORD', 'ORD', 'OVERLAPS', 'PASSWORD', 'PERIOD_ADD', 'PERIOD_DIFF', 'PI', 'POINT', 'POINTFROMTEXT', 'POINTFROMWKB', 'POINTN', 'POINTONSURFACE',
        'POLYFROMTEXT', 'POLYFROMWKB', 'POLYGON', 'POLYGONFROMTEXT', 'POLYGONFROMWKB', 'POSITION', 'POW', 'POWER', 'QUARTER', 'QUOTE', 'RADIANS', 'RAND',
        'RELATED', 'RELEASE_LOCK', 'REPEAT', 'REPLACE', 'REVERSE', 'RIGHT', 'ROUND', 'ROW_COUNT', 'RPAD', 'RTRIM', 'SCHEMA', 'SECOND', 'SEC_TO_TIME',
        'SESSION_USER', 'SHA', 'SHA1', 'SIGN', 'SIN', 'SLEEP', 'SOUNDEX', 'SPACE', 'SQRT', 'SRID', 'STARTPOINT', 'STD', 'STDDEV', 'STDDEV_POP', 'STDDEV_SAMP',
        'STRCMP', 'STR_TO_DATE', 'SUBDATE', 'SUBSTR', 'SUBSTRING', 'SUBSTRING_INDEX', 'SUBTIME', 'SUM', 'SYMDIFFERENCE', 'SYSDATE', 'SYSTEM_USER', 'TAN',
        'TIME', 'TIMEDIFF', 'TIMESTAMP', 'TIMESTAMPADD', 'TIMESTAMPDIFF', 'TIME_FORMAT', 'TIME_TO_SEC', 'TOUCHES', 'TO_DAYS', 'TRIM', 'TRUNCATE', 'UCASE',
        'UNCOMPRESS', 'UNCOMPRESSED_LENGTH', 'UNHEX', 'UNIQUE_USERS', 'UNIX_TIMESTAMP', 'UPDATEXML', 'UPPER', 'USER', 'UTC_DATE', 'UTC_TIME', 'UTC_TIMESTAMP',
        'UUID', 'VARIANCE', 'VAR_POP', 'VAR_SAMP', 'VERSION', 'WEEK', 'WEEKDAY', 'WEEKOFYEAR', 'WITHIN', 'X', 'Y', 'YEAR', 'YEARWEEK'
    );

    // Punctuation that can be used as a boundary between other tokens
    protected static $boundaries = array(',', ';',':', ')', '(', '.', '=', '<', '>', '+', '-', '*', '/', '!', '^', '%', '|', '&', '#');

    // For HTML syntax highlighting
    // Styles applied to different token types
    public static $quote_attributes = 'style="color: blue;"';
    public static $backtick_quote_attributes = 'style="color: purple;"';
    public static $reserved_attributes = 'style="font-weight:bold;"';
    public static $boundary_attributes = '';
    public static $number_attributes = 'style="color: green;"';
    public static $word_attributes = 'style="color: #333;"';
    public static $error_attributes = 'style="background-color: red;"';
    public static $comment_attributes = 'style="color: #aaa;"';
    public static $variable_attributes = 'style="color: orange;"';
    public static $pre_attributes = 'style="color: black; background-color: white;"';

    // Boolean - whether or not the current environment is the CLI
    // This affects the type of syntax highlighting
    // If not defined, it will be determined automatically
    public static $cli;

    // For CLI syntax highlighting
    public static $cli_quote = "\x1b[34;1m";
    public static $cli_backtick_quote = "\x1b[35;1m";
    public static $cli_reserved = "\x1b[37m";
    public static $cli_boundary = "";
    public static $cli_number = "\x1b[32;1m";
    public static $cli_word = "";
    public static $cli_error = "\x1b[31;1;7m";
    public static $cli_comment = "\x1b[30;1m";
    public static $cli_functions = "\x1b[37m";
    public static $cli_variable = "\x1b[36;1m";

    // The tab character to use when formatting SQL
    public static $tab = '  ';

    // This flag tells us if queries need to be enclosed in <pre> tags
    public static $use_pre = true;

    // This flag tells us if SqlFormatted has been initialized
    protected static $init;

    // Regular expressions for tokenizing
    protected static $regex_boundaries;
    protected static $regex_reserved;
    protected static $regex_reserved_newline;
    protected static $regex_reserved_toplevel;
    protected static $regex_function;

    // Cache variables
    // Only tokens shorter than this size will be cached.  Somewhere between 10 and 20 seems to work well for most cases.
    public static $max_cachekey_size = 15;
    protected static $token_cache = array();
    protected static $cache_hits = 0;
    protected static $cache_misses = 0;

    /**
     * Get stats about the token cache
     * @return Array An array containing the keys 'hits', 'misses', 'entries', and 'size' in bytes
     */
    public static function getCacheStats()
    {
        return array(
            'hits'=>self::$cache_hits,
            'misses'=>self::$cache_misses,
            'entries'=>count(self::$token_cache),
            'size'=>strlen(serialize(self::$token_cache))
        );
    }

    /**
     * Stuff that only needs to be done once.  Builds regular expressions and sorts the reserved words.
     */
    protected static function init()
    {
        if (self::$init) return;

        // Sort reserved word list from longest word to shortest, 3x faster than usort
        $reservedMap = array_combine(self::$reserved, array_map('strlen', self::$reserved));
        arsort($reservedMap);
        self::$reserved = array_keys($reservedMap);

        // Set up regular expressions
        self::$regex_boundaries = '('.implode('|',array_map(array(__CLASS__, 'quote_regex'),self::$boundaries)).')';
        self::$regex_reserved = '('.implode('|',array_map(array(__CLASS__, 'quote_regex'),self::$reserved)).')';
        self::$regex_reserved_toplevel = str_replace(' ','\\s+','('.implode('|',array_map(array(__CLASS__, 'quote_regex'),self::$reserved_toplevel)).')');
        self::$regex_reserved_newline = str_replace(' ','\\s+','('.implode('|',array_map(array(__CLASS__, 'quote_regex'),self::$reserved_newline)).')');

        self::$regex_function = '('.implode('|',array_map(array(__CLASS__, 'quote_regex'),self::$functions)).')';

        self::$init = true;
    }

    /**
     * Return the next token and token type in a SQL string.
     * Quoted strings, comments, reserved words, whitespace, and punctuation are all their own tokens.
     *
     * @param String $string   The SQL string
     * @param array  $previous The result of the previous getNextToken() call
     *
     * @return Array An associative array containing the type and value of the token.
     */
    protected static function getNextToken($string, $previous = null)
    {
        // Whitespace
        if (preg_match('/^\s+/',$string,$matches)) {
            return array(
                self::TOKEN_VALUE => $matches[0],
                self::TOKEN_TYPE=>self::TOKEN_TYPE_WHITESPACE
            );
        }

        // Comment
        if ($string[0] === '#' || (isset($string[1])&&($string[0]==='-'&&$string[1]==='-') || ($string[0]==='/'&&$string[1]==='*'))) {
            // Comment until end of line
            if ($string[0] === '-' || $string[0] === '#') {
                $last = strpos($string, "\n");
                $type = self::TOKEN_TYPE_COMMENT;
            } else { // Comment until closing comment tag
                $last = strpos($string, "*/", 2) + 2;
                $type = self::TOKEN_TYPE_BLOCK_COMMENT;
            }

            if ($last === false) {
                $last = strlen($string);
            }

            return array(
                self::TOKEN_VALUE => substr($string, 0, $last),
                self::TOKEN_TYPE  => $type
            );
        }

        // Quoted String
        if ($string[0]==='"' || $string[0]==='\'' || $string[0]==='`' || $string[0]==='[') {
            $return = array(
                self::TOKEN_TYPE => (($string[0]==='`' || $string[0]==='[')? self::TOKEN_TYPE_BACKTICK_QUOTE : self::TOKEN_TYPE_QUOTE),
                self::TOKEN_VALUE => self::getQuotedString($string)
            );

            return $return;
        }

        // User-defined Variable
        if (($string[0] === '@' || $string[0] === ':') && isset($string[1])) {
            $ret = array(
                self::TOKEN_VALUE => null,
                self::TOKEN_TYPE => self::TOKEN_TYPE_VARIABLE
            );

            // If the variable name is quoted
            if ($string[1]==='"' || $string[1]==='\'' || $string[1]==='`') {
                $ret[self::TOKEN_VALUE] = $string[0].self::getQuotedString(substr($string,1));
            }
            // Non-quoted variable name
            else {
                preg_match('/^('.$string[0].'[a-zA-Z0-9\._\$]+)/',$string,$matches);
                if ($matches) {
                    $ret[self::TOKEN_VALUE] = $matches[1];
                }
            }

            if($ret[self::TOKEN_VALUE] !== null) return $ret;
        }

        // Number (decimal, binary, or hex)
        if (preg_match('/^([0-9]+(\.[0-9]+)?|0x[0-9a-fA-F]+|0b[01]+)($|\s|"\'`|'.self::$regex_boundaries.')/',$string,$matches)) {
            return array(
                self::TOKEN_VALUE => $matches[1],
                self::TOKEN_TYPE=>self::TOKEN_TYPE_NUMBER
            );
        }

        // Boundary Character (punctuation and symbols)
        if (preg_match('/^('.self::$regex_boundaries.')/',$string,$matches)) {
            return array(
                self::TOKEN_VALUE => $matches[1],
                self::TOKEN_TYPE  => self::TOKEN_TYPE_BOUNDARY
            );
        }

        // A reserved word cannot be preceded by a '.'
        // this makes it so in "mytable.from", "from" is not considered a reserved word
        if (!$previous || !isset($previous[self::TOKEN_VALUE]) || $previous[self::TOKEN_VALUE] !== '.') {
            $upper = strtoupper($string);
            // Top Level Reserved Word
            if (preg_match('/^('.self::$regex_reserved_toplevel.')($|\s|'.self::$regex_boundaries.')/', $upper,$matches)) {
                return array(
                    self::TOKEN_TYPE=>self::TOKEN_TYPE_RESERVED_TOPLEVEL,
                    self::TOKEN_VALUE=>substr($string,0,strlen($matches[1]))
                );
            }
            // Newline Reserved Word
            if (preg_match('/^('.self::$regex_reserved_newline.')($|\s|'.self::$regex_boundaries.')/', $upper,$matches)) {
                return array(
                    self::TOKEN_TYPE=>self::TOKEN_TYPE_RESERVED_NEWLINE,
                    self::TOKEN_VALUE=>substr($string,0,strlen($matches[1]))
                );
            }
            // Other Reserved Word
            if (preg_match('/^('.self::$regex_reserved.')($|\s|'.self::$regex_boundaries.')/', $upper,$matches)) {
                return array(
                    self::TOKEN_TYPE=>self::TOKEN_TYPE_RESERVED,
                    self::TOKEN_VALUE=>substr($string,0,strlen($matches[1]))
                );
            }
        }

        // A function must be suceeded by '('
        // this makes it so "count(" is considered a function, but "count" alone is not
        $upper = strtoupper($string);
        // function
        if (preg_match('/^('.self::$regex_function.'[(]|\s|[)])/', $upper,$matches)) {
            return array(
                self::TOKEN_TYPE=>self::TOKEN_TYPE_RESERVED,
                self::TOKEN_VALUE=>substr($string,0,strlen($matches[1])-1)
            );
        }

        // Non reserved word
        preg_match('/^(.*?)($|\s|["\'`]|'.self::$regex_boundaries.')/',$string,$matches);

        return array(
            self::TOKEN_VALUE => $matches[1],
            self::TOKEN_TYPE  => self::TOKEN_TYPE_WORD
        );
    }

    protected static function getQuotedString($string)
    {
        $ret = null;

        // This checks for the following patterns:
        // 1. backtick quoted string using `` to escape
        // 2. square bracket quoted string (SQL Server) using ]] to escape
        // 3. double quoted string using "" or \" to escape
        // 4. single quoted string using '' or \' to escape
        if ( preg_match('/^(((`[^`]*($|`))+)|((\[[^\]]*($|\]))(\][^\]]*($|\]))*)|(("[^"\\\\]*(?:\\\\.[^"\\\\]*)*("|$))+)|((\'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*(\'|$))+))/s', $string, $matches)) {
            $ret = $matches[1];
        }

        return $ret;
    }

    /**
     * Takes a SQL string and breaks it into tokens.
     * Each token is an associative array with type and value.
     *
     * @param String $string The SQL string
     *
     * @return Array An array of tokens.
     */
    protected static function tokenize($string)
    {
        self::init();

        $tokens = array();

        // Used for debugging if there is an error while tokenizing the string
        $original_length = strlen($string);

        // Used to make sure the string keeps shrinking on each iteration
        $old_string_len = strlen($string) + 1;

        $token = null;

        $current_length = strlen($string);

        // Keep processing the string until it is empty
        while ($current_length) {
            // If the string stopped shrinking, there was a problem
            if ($old_string_len <= $current_length) {
                $tokens[] = array(
                    self::TOKEN_VALUE=>$string,
                    self::TOKEN_TYPE=>self::TOKEN_TYPE_ERROR
                );

                return $tokens;
            }
            $old_string_len =  $current_length;

            // Determine if we can use caching
            if ($current_length >= self::$max_cachekey_size) {
                $cacheKey = substr($string,0,self::$max_cachekey_size);
            } else {
                $cacheKey = false;
            }

            // See if the token is already cached
            if ($cacheKey && isset(self::$token_cache[$cacheKey])) {
                // Retrieve from cache
                $token = self::$token_cache[$cacheKey];
                $token_length = strlen($token[self::TOKEN_VALUE]);
                self::$cache_hits++;
            } else {
                // Get the next token and the token type
                $token = self::getNextToken($string, $token);
                $token_length = strlen($token[self::TOKEN_VALUE]);
                self::$cache_misses++;

                // If the token is shorter than the max length, store it in cache
                if ($cacheKey && $token_length < self::$max_cachekey_size) {
                    self::$token_cache[$cacheKey] = $token;
                }
            }

            $tokens[] = $token;

            // Advance the string
            $string = substr($string, $token_length);

            $current_length -= $token_length;
        }

        return $tokens;
    }

    /**
     * Format the whitespace in a SQL string to make it easier to read.
     *
     * @param String  $string    The SQL string
     * @param boolean $highlight If true, syntax highlighting will also be performed
     *
     * @return String The SQL string with HTML styles and formatting wrapped in a <pre> tag
     */
    public static function format($string, $highlight=true)
    {
        // This variable will be populated with formatted html
        $return = '';

        // Use an actual tab while formatting and then switch out with self::$tab at the end
        $tab = "\t";

        $indent_level = 0;
        $newline = false;
        $inline_parentheses = false;
        $increase_special_indent = false;
        $increase_block_indent = false;
        $indent_types = array();
        $added_newline = false;
        $inline_count = 0;
        $inline_indented = false;
        $clause_limit = false;

        // Tokenize String
        $original_tokens = self::tokenize($string);

        // Remove existing whitespace
        $tokens = array();
        foreach ($original_tokens as $i=>$token) {
            if ($token[self::TOKEN_TYPE] !== self::TOKEN_TYPE_WHITESPACE) {
                $token['i'] = $i;
                $tokens[] = $token;
            }
        }

        // Format token by token
        foreach ($tokens as $i=>$token) {
            // Get highlighted token if doing syntax highlighting
            if ($highlight) {
                $highlighted = self::highlightToken($token);
            } else { // If returning raw text
                $highlighted = $token[self::TOKEN_VALUE];
            }

            // If we are increasing the special indent level now
            if ($increase_special_indent) {
                $indent_level++;
                $increase_special_indent = false;
                array_unshift($indent_types,'special');
            }
            // If we are increasing the block indent level now
            if ($increase_block_indent) {
                $indent_level++;
                $increase_block_indent = false;
                array_unshift($indent_types,'block');
            }

            // If we need a new line before the token
            if ($newline) {
                $return .= "\n" . str_repeat($tab, $indent_level);
                $newline = false;
                $added_newline = true;
            } else {
                $added_newline = false;
            }

            // Display comments directly where they appear in the source
            if ($token[self::TOKEN_TYPE] === self::TOKEN_TYPE_COMMENT || $token[self::TOKEN_TYPE] === self::TOKEN_TYPE_BLOCK_COMMENT) {
                if ($token[self::TOKEN_TYPE] === self::TOKEN_TYPE_BLOCK_COMMENT) {
                    $indent = str_repeat($tab,$indent_level);
                    $return .= "\n" . $indent;
                    $highlighted = str_replace("\n","\n".$indent,$highlighted);
                }

                $return .= $highlighted;
                $newline = true;
                continue;
            }

            if ($inline_parentheses) {
                // End of inline parentheses
                if ($token[self::TOKEN_VALUE] === ')') {
                    $return = rtrim($return,' ');

                    if ($inline_indented) {
                        array_shift($indent_types);
                        $indent_level --;
                        $return .= "\n" . str_repeat($tab, $indent_level);
                    }

                    $inline_parentheses = false;

                    $return .= $highlighted . ' ';
                    continue;
                }

                if ($token[self::TOKEN_VALUE] === ',') {
                    if ($inline_count >= 30) {
                        $inline_count = 0;
                        $newline = true;
                    }
                }

                $inline_count += strlen($token[self::TOKEN_VALUE]);
            }

            // Opening parentheses increase the block indent level and start a new line
            if ($token[self::TOKEN_VALUE] === '(') {
                // First check if this should be an inline parentheses block
                // Examples are "NOW()", "COUNT(*)", "int(10)", key(`somecolumn`), DECIMAL(7,2)
                // Allow up to 3 non-whitespace tokens inside inline parentheses
                $length = 0;
                for ($j=1;$j<=250;$j++) {
                    // Reached end of string
                    if (!isset($tokens[$i+$j])) break;

                    $next = $tokens[$i+$j];

                    // Reached closing parentheses, able to inline it
                    if ($next[self::TOKEN_VALUE] === ')') {
                        $inline_parentheses = true;
                        $inline_count = 0;
                        $inline_indented = false;
                        break;
                    }

                    // Reached an invalid token for inline parentheses
                    if ($next[self::TOKEN_VALUE]===';' || $next[self::TOKEN_VALUE]==='(') {
                        break;
                    }

                    // Reached an invalid token type for inline parentheses
                    if ($next[self::TOKEN_TYPE]===self::TOKEN_TYPE_RESERVED_TOPLEVEL || $next[self::TOKEN_TYPE]===self::TOKEN_TYPE_RESERVED_NEWLINE || $next[self::TOKEN_TYPE]===self::TOKEN_TYPE_COMMENT || $next[self::TOKEN_TYPE]===self::TOKEN_TYPE_BLOCK_COMMENT) {
                        break;
                    }

                    $length += strlen($next[self::TOKEN_VALUE]);
                }

                if ($inline_parentheses && $length > 30) {
                    $increase_block_indent = true;
                    $inline_indented = true;
                    $newline = true;
                }

                // Take out the preceding space unless there was whitespace there in the original query
                if (isset($original_tokens[$token['i']-1]) && $original_tokens[$token['i']-1][self::TOKEN_TYPE] !== self::TOKEN_TYPE_WHITESPACE) {
                    $return = rtrim($return,' ');
                }

                if (!$inline_parentheses) {
                    $increase_block_indent = true;
                    // Add a newline after the parentheses
                    $newline = true;
                }

            }

            // Closing parentheses decrease the block indent level
            elseif ($token[self::TOKEN_VALUE] === ')') {
                // Remove whitespace before the closing parentheses
                $return = rtrim($return,' ');

                $indent_level--;

                // Reset indent level
                while ($j=array_shift($indent_types)) {
                    if ($j==='special') {
                        $indent_level--;
                    } else {
                        break;
                    }
                }

                if ($indent_level < 0) {
                    // This is an error
                    $indent_level = 0;

                    if ($highlight) {
                        $return .= "\n".self::highlightError($token[self::TOKEN_VALUE]);
                        continue;
                    }
                }

                // Add a newline before the closing parentheses (if not already added)
                if (!$added_newline) {
                    $return .= "\n" . str_repeat($tab, $indent_level);
                }
            }

            // Top level reserved words start a new line and increase the special indent level
            elseif ($token[self::TOKEN_TYPE] === self::TOKEN_TYPE_RESERVED_TOPLEVEL) {
                $increase_special_indent = true;

                // If the last indent type was 'special', decrease the special indent for this round
                reset($indent_types);
                if (current($indent_types)==='special') {
                    $indent_level--;
                    array_shift($indent_types);
                }

                // Add a newline after the top level reserved word
                $newline = true;
                // Add a newline before the top level reserved word (if not already added)
                if (!$added_newline) {
                    $return .= "\n" . str_repeat($tab, $indent_level);
                }
                // If we already added a newline, redo the indentation since it may be different now
                else {
                    $return = rtrim($return,$tab).str_repeat($tab, $indent_level);
                }

                // If the token may have extra whitespace
                if (strpos($token[self::TOKEN_VALUE],' ')!==false || strpos($token[self::TOKEN_VALUE],"\n")!==false || strpos($token[self::TOKEN_VALUE],"\t")!==false) {
                    $highlighted = preg_replace('/\s+/',' ',$highlighted);
                }
                //if SQL 'LIMIT' clause, start variable to reset newline
                if ($token[self::TOKEN_VALUE] === 'LIMIT' && !$inline_parentheses) {
                    $clause_limit = true;
                }
            }

            // Checks if we are out of the limit clause
            elseif ($clause_limit && $token[self::TOKEN_VALUE] !== "," && $token[self::TOKEN_TYPE] !== self::TOKEN_TYPE_NUMBER && $token[self::TOKEN_TYPE] !== self::TOKEN_TYPE_WHITESPACE) {
                $clause_limit = false;
            }

            // Commas start a new line (unless within inline parentheses or SQL 'LIMIT' clause)
            elseif ($token[self::TOKEN_VALUE] === ',' && !$inline_parentheses) {
                //If the previous TOKEN_VALUE is 'LIMIT', resets new line
                if ($clause_limit === true) {
                    $newline = false;
                    $clause_limit = false;
                }
                // All other cases of commas
                else {
                    $newline = true;
                }
            }

            // Newline reserved words start a new line
            elseif ($token[self::TOKEN_TYPE] === self::TOKEN_TYPE_RESERVED_NEWLINE) {
                // Add a newline before the reserved word (if not already added)
                if (!$added_newline) {
                    $return .= "\n" . str_repeat($tab, $indent_level);
                }

                // If the token may have extra whitespace
                if (strpos($token[self::TOKEN_VALUE],' ')!==false || strpos($token[self::TOKEN_VALUE],"\n")!==false || strpos($token[self::TOKEN_VALUE],"\t")!==false) {
                    $highlighted = preg_replace('/\s+/',' ',$highlighted);
                }
            }

            // Multiple boundary characters in a row should not have spaces between them (not including parentheses)
            elseif ($token[self::TOKEN_TYPE] === self::TOKEN_TYPE_BOUNDARY) {
                if (isset($tokens[$i-1]) && $tokens[$i-1][self::TOKEN_TYPE] === self::TOKEN_TYPE_BOUNDARY) {
                    if (isset($original_tokens[$token['i']-1]) && $original_tokens[$token['i']-1][self::TOKEN_TYPE] !== self::TOKEN_TYPE_WHITESPACE) {
                        $return = rtrim($return,' ');
                    }
                }
            }

            // If the token shouldn't have a space before it
            if ($token[self::TOKEN_VALUE] === '.' || $token[self::TOKEN_VALUE] === ',' || $token[self::TOKEN_VALUE] === ';') {
                $return = rtrim($return, ' ');
            }

            $return .= $highlighted.' ';

            // If the token shouldn't have a space after it
            if ($token[self::TOKEN_VALUE] === '(' || $token[self::TOKEN_VALUE] === '.') {
                $return = rtrim($return,' ');
            }

            // If this is the "-" of a negative number, it shouldn't have a space after it
            if($token[self::TOKEN_VALUE] === '-' && isset($tokens[$i+1]) && $tokens[$i+1][self::TOKEN_TYPE] === self::TOKEN_TYPE_NUMBER && isset($tokens[$i-1])) {
                $prev = $tokens[$i-1][self::TOKEN_TYPE];
                if($prev !== self::TOKEN_TYPE_QUOTE && $prev !== self::TOKEN_TYPE_BACKTICK_QUOTE && $prev !== self::TOKEN_TYPE_WORD && $prev !== self::TOKEN_TYPE_NUMBER) {
                    $return = rtrim($return,' ');
                }
            }
        }

        // If there are unmatched parentheses
        if ($highlight && array_search('block',$indent_types) !== false) {
            $return .= "\n".self::highlightError("WARNING: unclosed parentheses or section");
        }

        // Replace tab characters with the configuration tab character
        $return = trim(str_replace("\t",self::$tab,$return));

        if ($highlight) {
            $return = self::output($return);
        }

        return $return;
    }

    /**
     * Add syntax highlighting to a SQL string
     *
     * @param String $string The SQL string
     *
     * @return String The SQL string with HTML styles applied
     */
    public static function highlight($string)
    {
        $tokens = self::tokenize($string);

        $return = '';

        foreach ($tokens as $token) {
            $return .= self::highlightToken($token);
        }

        return self::output($return);
    }

    /**
     * Split a SQL string into multiple queries.
     * Uses ";" as a query delimiter.
     *
     * @param String $string The SQL string
     *
     * @return Array An array of individual query strings without trailing semicolons
     */
    public static function splitQuery($string)
    {
        $queries = array();
        $current_query = '';
        $empty = true;

        $tokens = self::tokenize($string);

        foreach ($tokens as $token) {
            // If this is a query separator
            if ($token[self::TOKEN_VALUE] === ';') {
                if (!$empty) {
                    $queries[] = $current_query.';';
                }
                $current_query = '';
                $empty = true;
                continue;
            }

            // If this is a non-empty character
            if ($token[self::TOKEN_TYPE] !== self::TOKEN_TYPE_WHITESPACE && $token[self::TOKEN_TYPE] !== self::TOKEN_TYPE_COMMENT && $token[self::TOKEN_TYPE] !== self::TOKEN_TYPE_BLOCK_COMMENT) {
                $empty = false;
            }

            $current_query .= $token[self::TOKEN_VALUE];
        }

        if (!$empty) {
            $queries[] = trim($current_query);
        }

        return $queries;
    }

    /**
     * Remove all comments from a SQL string
     *
     * @param String $string The SQL string
     *
     * @return String The SQL string without comments
     */
    public static function removeComments($string)
    {
        $result = '';

        $tokens = self::tokenize($string);

        foreach ($tokens as $token) {
            // Skip comment tokens
            if ($token[self::TOKEN_TYPE] === self::TOKEN_TYPE_COMMENT || $token[self::TOKEN_TYPE] === self::TOKEN_TYPE_BLOCK_COMMENT) {
                continue;
            }

            $result .= $token[self::TOKEN_VALUE];
        }
        $result = self::format( $result,false);

        return $result;
    }

    /**
     * Compress a query by collapsing white space and removing comments
     *
     * @param String $string The SQL string
     *
     * @return String The SQL string without comments
     */
    public static function compress($string)
    {
        $result = '';

        $tokens = self::tokenize($string);

        $whitespace = true;
        foreach ($tokens as $token) {
            // Skip comment tokens
            if ($token[self::TOKEN_TYPE] === self::TOKEN_TYPE_COMMENT || $token[self::TOKEN_TYPE] === self::TOKEN_TYPE_BLOCK_COMMENT) {
                continue;
            }
            // Remove extra whitespace in reserved words (e.g "OUTER     JOIN" becomes "OUTER JOIN")
            elseif ($token[self::TOKEN_TYPE] === self::TOKEN_TYPE_RESERVED || $token[self::TOKEN_TYPE] === self::TOKEN_TYPE_RESERVED_NEWLINE || $token[self::TOKEN_TYPE] === self::TOKEN_TYPE_RESERVED_TOPLEVEL) {
                $token[self::TOKEN_VALUE] = preg_replace('/\s+/',' ',$token[self::TOKEN_VALUE]);
            }

            if ($token[self::TOKEN_TYPE] === self::TOKEN_TYPE_WHITESPACE) {
                // If the last token was whitespace, don't add another one
                if ($whitespace) {
                    continue;
                } else {
                    $whitespace = true;
                    // Convert all whitespace to a single space
                    $token[self::TOKEN_VALUE] = ' ';
                }
            } else {
                $whitespace = false;
            }

            $result .= $token[self::TOKEN_VALUE];
        }

        return rtrim($result);
    }

    /**
     * Highlights a token depending on its type.
     *
     * @param Array $token An associative array containing type and value.
     *
     * @return String HTML code of the highlighted token.
     */
    protected static function highlightToken($token)
    {
        $type = $token[self::TOKEN_TYPE];

        if (self::is_cli()) {
            $token = $token[self::TOKEN_VALUE];
        } else {
            if (defined('ENT_IGNORE')) {
                $token = htmlentities($token[self::TOKEN_VALUE],ENT_COMPAT | ENT_IGNORE ,'UTF-8');
            } else {
                $token = htmlentities($token[self::TOKEN_VALUE],ENT_COMPAT,'UTF-8');
            }
        }

        if ($type===self::TOKEN_TYPE_BOUNDARY) {
            return self::highlightBoundary($token);
        } elseif ($type===self::TOKEN_TYPE_WORD) {
            return self::highlightWord($token);
        } elseif ($type===self::TOKEN_TYPE_BACKTICK_QUOTE) {
            return self::highlightBacktickQuote($token);
        } elseif ($type===self::TOKEN_TYPE_QUOTE) {
            return self::highlightQuote($token);
        } elseif ($type===self::TOKEN_TYPE_RESERVED) {
            return self::highlightReservedWord($token);
        } elseif ($type===self::TOKEN_TYPE_RESERVED_TOPLEVEL) {
            return self::highlightReservedWord($token);
        } elseif ($type===self::TOKEN_TYPE_RESERVED_NEWLINE) {
            return self::highlightReservedWord($token);
        } elseif ($type===self::TOKEN_TYPE_NUMBER) {
            return self::highlightNumber($token);
        } elseif ($type===self::TOKEN_TYPE_VARIABLE) {
            return self::highlightVariable($token);
        } elseif ($type===self::TOKEN_TYPE_COMMENT || $type===self::TOKEN_TYPE_BLOCK_COMMENT) {
            return self::highlightComment($token);
        }

        return $token;
    }

    /**
     * Highlights a quoted string
     *
     * @param String $value The token's value
     *
     * @return String HTML code of the highlighted token.
     */
    protected static function highlightQuote($value)
    {
        if (self::is_cli()) {
            return self::$cli_quote . $value . "\x1b[0m";
        } else {
            return '<span ' . self::$quote_attributes . '>' . $value . '</span>';
        }
    }

    /**
     * Highlights a backtick quoted string
     *
     * @param String $value The token's value
     *
     * @return String HTML code of the highlighted token.
     */
    protected static function highlightBacktickQuote($value)
    {
        if (self::is_cli()) {
            return self::$cli_backtick_quote . $value . "\x1b[0m";
        } else {
            return '<span ' . self::$backtick_quote_attributes . '>' . $value . '</span>';
        }
    }

    /**
     * Highlights a reserved word
     *
     * @param String $value The token's value
     *
     * @return String HTML code of the highlighted token.
     */
    protected static function highlightReservedWord($value)
    {
        if (self::is_cli()) {
            return self::$cli_reserved . $value . "\x1b[0m";
        } else {
            return '<span ' . self::$reserved_attributes . '>' . $value . '</span>';
        }
    }

    /**
     * Highlights a boundary token
     *
     * @param String $value The token's value
     *
     * @return String HTML code of the highlighted token.
     */
    protected static function highlightBoundary($value)
    {
        if ($value==='(' || $value===')') return $value;

        if (self::is_cli()) {
            return self::$cli_boundary . $value . "\x1b[0m";
        } else {
            return '<span ' . self::$boundary_attributes . '>' . $value . '</span>';
        }
    }

    /**
     * Highlights a number
     *
     * @param String $value The token's value
     *
     * @return String HTML code of the highlighted token.
     */
    protected static function highlightNumber($value)
    {
        if (self::is_cli()) {
            return self::$cli_number . $value . "\x1b[0m";
        } else {
            return '<span ' . self::$number_attributes . '>' . $value . '</span>';
        }
    }

    /**
     * Highlights an error
     *
     * @param String $value The token's value
     *
     * @return String HTML code of the highlighted token.
     */
    protected static function highlightError($value)
    {
        if (self::is_cli()) {
            return self::$cli_error . $value . "\x1b[0m";
        } else {
            return '<span ' . self::$error_attributes . '>' . $value . '</span>';
        }
    }

    /**
     * Highlights a comment
     *
     * @param String $value The token's value
     *
     * @return String HTML code of the highlighted token.
     */
    protected static function highlightComment($value)
    {
        if (self::is_cli()) {
            return self::$cli_comment . $value . "\x1b[0m";
        } else {
            return '<span ' . self::$comment_attributes . '>' . $value . '</span>';
        }
    }

    /**
     * Highlights a word token
     *
     * @param String $value The token's value
     *
     * @return String HTML code of the highlighted token.
     */
    protected static function highlightWord($value)
    {
        if (self::is_cli()) {
            return self::$cli_word . $value . "\x1b[0m";
        } else {
            return '<span ' . self::$word_attributes . '>' . $value . '</span>';
        }
    }

    /**
     * Highlights a variable token
     *
     * @param String $value The token's value
     *
     * @return String HTML code of the highlighted token.
     */
    protected static function highlightVariable($value)
    {
        if (self::is_cli()) {
            return self::$cli_variable . $value . "\x1b[0m";
        } else {
            return '<span ' . self::$variable_attributes . '>' . $value . '</span>';
        }
    }

    /**
     * Helper function for building regular expressions for reserved words and boundary characters
     *
     * @param String $a The string to be quoted
     *
     * @return String The quoted string
     */
    private static function quote_regex($a)
    {
        return preg_quote($a,'/');
    }

    /**
     * Helper function for building string output
     *
     * @param String $string The string to be quoted
     *
     * @return String The quoted string
     */
    private static function output($string)
    {
        if (self::is_cli()) {
            return $string."\n";
        } else {
            $string=trim($string);
            if (!self::$use_pre) {
                return $string;
            }

            return '<pre '.self::$pre_attributes.'>' . $string . '</pre>';
        }
    }

    private static function is_cli()
    {
        if (isset(self::$cli)) return self::$cli;
        else return php_sapi_name() === 'cli';
    }

}