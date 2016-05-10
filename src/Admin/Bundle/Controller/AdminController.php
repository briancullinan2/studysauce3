<?php

namespace  {

    use Symfony\Bundle\FrameworkBundle\Templating\TimedPhpEngine;
    use Symfony\Bundle\FrameworkBundle\Templating\PhpEngine;
    use Wa72\HtmlPageDom\HtmlPage;
    use Wa72\HtmlPageDom\HtmlPageCrawler;

    if (!function_exists('jQuery')) {
        /**
         * @param $context
         * @return HtmlPageCrawler
         */
        function jQuery($context)
        {
            if ($context instanceof PhpEngine) {
                return HtmlPageCrawler::create('<div/>')->find('div');
            }
            return HtmlPageCrawler::create($context);
        }
    }

    if (!function_exists('concat')) {
        function concat()
        {
            return implode('', func_get_args());
        }
    }
}

namespace Admin\Bundle\Controller {

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
    use Symfony\Bundle\FrameworkBundle\Templating\TimedPhpEngine;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
    use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
    use Symfony\Component\Security\Acl\Dbal\AclProvider;
    use Symfony\Component\Security\Acl\Dbal\MutableAclProvider;
    use Symfony\Component\Security\Acl\Domain\Acl;
    use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
    use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
    use Wa72\HtmlPageDom\HtmlPage;

    /**
     * Class PartnerController
     * @package StudySauce\Bundle\Controller
     */
    class AdminController extends Controller
    {
        public static $radioCounter = 100000;

        /** @var ClassMetadata[] $allTables */
        public static $allTables;
        /** @var ClassMetadata[] $allTableMetadata */
        public static $allTableMetadata;
        /** @var string[] $allTableClasses */
        public static $allTableClasses;
        /** @var array $defaultTables A list of all available fields, firewall */
        public static $defaultTables = [ // database table and field firewall
            // TODO: simplify this maybe by specifying 'ss_user' => 'name' => 'authored,userPacks.pack'
            'ss_user' => ['id' => ['lastVisit', 'created', 'id'], 'name' => ['first', 'last', 'email'], 'groups', 'packs' => ['authored', 'userPacks.pack'], 'roles', 'actions' => ['deleted']],
            'ss_group' => ['id' => ['created', 'id', 'upload'], 'name' => ['name', 'userCountStr', 'descriptionStr'], 'parent', 'invites', 'packs' => ['packs', 'groupPacks'], 'actions' => ['deleted']],
            'pack' => ['id' => ['modified', 'created', 'id', 'upload'], 'name' => ['title', 'userCountStr', 'cardCountStr'], 'status', ['group', 'groups', 'user', 'userPacks.user'], 'properties', 'actions'],
            'card' => ['id' => ['type', 'upload'], 'name' => ['content'], 'correct' => ['correct', 'answers'], ['pack'], 'actions' => ['deleted']],
            'invite' => ['id', 'name' => ['code', 'email', 'created'], 'actions' => ['deleted']],
            'user_pack' => ['user', 'pack', 'removed']
            // TODO: this really generalized template
            //'invite' => ['id', 'code', 'groups', 'users', 'properties', 'actions']
        ];

        public static $defaultMiniTables = [
            'pack' => ['title', 'userCountStr', 'cardCountStr', 'id', 'status'],
            'ss_user' => ['first', 'last', 'email', 'id', 'deleted'],
            'ss_group' => ['name', 'userCountStr', 'descriptionStr', 'id', 'deleted']];

        public static $defaultSearch = ['tables' => ['ss_user', 'ss_group'], 'user_pack-removed' => false, 'ss_user-enabled' => true, 'ss_group-deleted' => false, 'parent-ss_group-deleted' => null, 'pack-status' => '!DELETED', 'card-deleted' => false];

        private static function getJoinTable($u)
        {
            if(is_array($u) && isset($u['table'])) {
                return $u['table'];
            }
            $type = get_class($u);
            $ti = array_search($type, self::$allTableClasses);
            if ($ti === false) {
                $type = get_parent_class($u);
                $ti = array_search($type, self::$allTableClasses);
            }
            if ($ti === false) {
                return false;
            }
            $joinTable = self::$allTableMetadata[$ti]->table['name'];
            return $joinTable;
        }


        private static function getSearchValue($field, $k, $f, $table, $request)
        {
            // search for unions in original request only
            if (isset($request[$table . '-' . $field])) {
                return [$table . '-' . $field, $request[$table . '-' . $field]];
            } else if (!empty($request[$field])) {
                return [$field, $request[$field]];
            } else if (is_array($f) && !empty($request[$k])) {
                return [$k, $request[$k]];
            } else if (!empty($request[$table])) {
                return [$table, $request[$table]];
            } else if (!empty($request['search'])) {
                return ['search', $request['search']];
            }
            return [null, null];
        }

        private static function joinBuilder(QueryBuilder $qb, $joinTable, $joinName, $field, $request, &$joins = [])
        {
            // TODO: add userPacks-removed = false search field
            $result = '';
            $joinFields = explode('.', $field);
            foreach ($joinFields as $jf) {
                $associated = self::$allTables[$joinTable]->getAssociationMappings();
                if (isset($associated[$jf])) {
                    $entity = $associated[$jf]['targetEntity'];
                    $ti = array_search($entity, self::$allTableClasses);
                    if ($ti !== false) {
                        $joinTable = self::$allTableMetadata[$ti]->table['name'];
                    } else {
                        continue;
                    }
                    $newName = $joinName . '_' . preg_replace('[^a-z]', '_', $jf) . $joinTable;
                    if (!in_array($newName, $joins)) {
                        $joins[] = $newName;
                        $qb = $qb->leftJoin($joinName . '.' . $jf, $newName);
                        if (in_array($joinTable, array_keys($request['tables']))) {
                            $result .= (!empty($result) ? ' AND ' : '') . self::searchBuilder($qb, $joinTable, $newName, $request, $joins);
                        }
                    }
                    $joinName = $newName;
                } else {
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
            } else {
                $searchTables = $request['tables'][$table];
            }
            $allFields = self::getAllFieldNames([$table => self::$defaultTables[$table]]);
            foreach ($searchTables as $k => $f) {
                if (!is_array($f)) {
                    $f = [$f];
                }
                foreach ($f as $field) {
                    // Skip restricted fields, all available fields are listed above
                    if (!in_array($field, $allFields)) {
                        continue;
                    }
                    $search = null;

                    // by default, columns searching on the same term are ORed together
                    list($searchField, $search) = self::getSearchValue($field, $k, $f, $table, $request);
                    if ($isDefault) {
                        $searchField .= '_default';
                    } else {

                        // only search joins on first connection
                        if ($table == $tableName) {
                            // remove table prefix from matching field
                            $joinRequest = [];
                            foreach ($request as $j => $r) {
                                $joinRequest[$j] = $r;
                                if (substr($j, 0, strlen($field) + 1) == $field . '-' && strpos($j, '-', strlen($field) + 1) !== false) {
                                    $joinRequest[substr($j, strlen($field) + 1)] = $r;
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
                    if (in_array($field, $fields)) {
                        list($searchWhere, $searchKey, $searchValue) = self::getWhereValue($search, $searchField, $field, $tableName);
                        if (!empty($searchWhere)) {
                            $where[$searchField] = (empty($where[$searchField]) ? '' : ($where[$searchField] . ' OR ')) . $searchWhere;
                            if (!empty($searchKey) && $qb->getParameters()->filter(function (Parameter $p) use ($searchKey) {
                                    return $p->getName() == $searchKey;
                                })->count() == 0
                            ) {
                                $qb = $qb->setParameter($searchKey, $searchValue);
                            }
                        }
                    }
                }
            }

            if (empty($where)) {
                return '';
            }
            // meet each criteria using AND
            return '(' . implode(' AND ', $where) . ') ';
        }

        private static function getWhereValue($search, $searchField, $field, $tableName)
        {
            if (substr($search, 0, 1) == '!') {
                $search = substr($search, 1);
                if (is_bool($search) || $search === 'false' || $search === 'true') {
                    $boolval = (is_string($search) ? filter_var($search, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : boolval($search));
                    return [$tableName . '.' . $field . ' != :' . $searchField . '_bool_not', $searchField . '_bool_not', $boolval];
                } else if (is_numeric($search)) {
                    return [$tableName . '.' . $field . ' != :' . $searchField . '_int_not', $searchField . '_int_not', intval($search)];
                } else if ($search == 'NULL') {
                    return [$tableName . '.' . $field . ' IS NOT NULL', null, null];
                } else {
                    return [$tableName . '.' . $field . ' NOT LIKE :' . $searchField . '_string_not', $searchField . '_string_not', '%' . $search . '%'];
                }
            } else {
                if (is_bool($search) || $search === 'false' || $search === 'true') {
                    $boolval = (is_string($search) ? filter_var($search, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : boolval($search));
                    return [$tableName . '.' . $field . ' = :' . $searchField . '_bool', $searchField . '_bool', $boolval];
                } else if (is_numeric($search)) {
                    return [$tableName . '.' . $field . ' = :' . $searchField . '_int', $searchField . '_int', intval($search)];
                } else if ($search == 'NULL') {
                    return [$tableName . '.' . $field . ' IS NULL', null, null];
                } else {
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

        public static function getAllFieldNames($tables)
        {
            $allFields = array_map(function ($t, $table) {
                $fields = array_map(function ($f, $k) use ($table) {
                    return is_array($f)
                        ? array_merge([$k], array_map(function ($field) use ($table) {
                            return $table . '-' . $field;
                        }, $f), array_map(function ($field) {
                            return $field;
                        }, $f))
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

        public function resultsAction(Request $request)
        {
            set_time_limit(0);
            /** @var $orm EntityManager */
            $orm = $this->get('doctrine')->getManager();

            /** @var $user User */
            $user = $this->getUser();

            if (empty($user) || !$user->hasRole('ROLE_ADMIN')) {
                throw new AccessDeniedHttpException();
            }

            self::$allTableClasses = $orm->getConfiguration()->getMetadataDriverImpl()->getAllClassNames();

            self::$allTableMetadata = array_map(function ($table) use ($orm) {
                return $orm->getMetadataFactory()->getMetadataFor($table);
            }, self::$allTableClasses);

            self::$allTables = array_combine(array_map(function (ClassMetadata $md) {
                return $md->getTableName();
            }, self::$allTableMetadata), self::$allTableMetadata);

            // pull out field searches
            $allFields = self::getAllFieldNames(self::$defaultTables);
            //$times = array_map(function($e) {
            /** @var User|Group $e */
            //    return $e->getCreated()->getTimestamp();
            //}, $entities);
            //array_multisort($times, SORT_NUMERIC, SORT_DESC, $entities);

            // default entities to show
            if (!empty($key = $request->get('requestKey'))) {
                $cacheRequest = $this->get('cache')->fetch($key);
                if (empty($cacheRequest)) {
                    throw new NotFoundHttpException('Could not find request key');
                } else {
                    $searchRequest = unserialize($cacheRequest);
                }
                if (!empty($tableView = $request->get('view')) && isset($searchRequest['views'][$tableView])) {
                    $searchRequest = array_merge($searchRequest, $searchRequest['views'][$tableView]);
                }
            } else {
                $searchRequest = array_merge($request->attributes->all(), $request->query->all());

                // setup the tables we are searching
                if (!isset($searchRequest['tables'])) {
                    $searchRequest['tables'] = [];
                }
                // convert table list to table => field list format
                $tblList = [];
                foreach ($searchRequest['tables'] as $table => $t) {
                    if (!is_array($t)) {
                        $table = $t;
                        $t = self::$defaultTables[explode('-', $t)[0]];
                    }
                    $tblList[$table] = $t;
                }
                $searchRequest['tables'] = $tblList;

                // TODO: fix this to do all occurrences and table names and other formats of searches specified above
                $regex = '/\s(' . implode('|', $allFields) . '):(.*)\s/i';
                if (isset($searchRequest['search']) && preg_match_all($regex, ' ' . $searchRequest['search'] . ' ', $matches)) {
                    foreach ($matches[0] as $i => $m) {
                        $searchRequest['search'] = str_replace(trim($m), '', $searchRequest['search']);
                        $searchRequest[$matches[1][$i]] = $matches[2][$i];
                    }
                }
            }

            $vars['search'] = '';
            foreach ($searchRequest['tables'] as $table => $t) {
                $tableParts = explode('-', $table);
                $ext = implode('-', array_splice($tableParts, 1));
                $table = explode('-', $table)[0];
                $aliasedRequest = [];
                if (strlen($ext) > 0) {
                    $ext = '-' . $ext;
                    $aliasLen = strlen($table) + strlen($ext);
                    foreach ($searchRequest as $r => $s) {
                        if (substr($r, 0, $aliasLen) == $table . $ext) {
                            $aliasedRequest[substr($r, $aliasLen)] = $s;
                        }
                    }
                    $aliasedRequest['tables'][$table] = $searchRequest['tables'][$table . $ext];
                }
                $aliasedRequest = array_merge($searchRequest, $aliasedRequest);
                $vars['tables'][$table . $ext] = $aliasedRequest['tables'][$table];

                $resultCount = isset($aliasedRequest['count-' . $table]) ? intval($aliasedRequest['count-' . $table]) : 25;
                // for templating purposes only
                if ($resultCount == -1 || isset($aliasedRequest['new']) && ($aliasedRequest['new'] === true || is_array($aliasedRequest['new']) && in_array($table, $aliasedRequest['new']))) {
                    $vars['results'][$table . $ext] = [];
                    $vars['results'][$table . $ext . '_total'] = 0;
                    continue;
                }

                /** @var QueryBuilder $qb */
                $qb = $orm->getRepository(self::$allTables[$table]->name)->createQueryBuilder($table);
                $joins = [];
                $where = self::searchBuilder($qb, $table, $table, $aliasedRequest, $joins);
                $defaultWhere = self::searchBuilder($qb, $table, $table, array_merge(array_diff_key(self::$defaultSearch, $aliasedRequest), ['tables' => [$table => self::$defaultTables[$table]]]), $joins, true);
                if (!empty($where)) {
                    $qb = $qb->where($where);
                }
                if (!empty($defaultWhere)) {
                    $qb = $qb->andWhere($defaultWhere);
                }

                $totalQuery = clone $qb;
                $query = $totalQuery->select('COUNT(DISTINCT(' . $table . '.' . self::$allTables[$table]->identifier[0] . '))')
                    ->getQuery();
                $total = $query->getSingleScalarResult();
                $vars['results'][$table . $ext . '_total'] = $total;

                // max pagination to search count
                if (!empty($resultCount) && isset($aliasedRequest['page-' . $table])) {
                    $page = $aliasedRequest['page-' . $table];
                    if ($page == 'last') {
                        $page = $total / $resultCount;
                    }
                    $resultOffset = (min(max(1, ceil($total / $resultCount)), max(1, intval($page))) - 1) * $resultCount;
                } else {
                    $resultOffset = 0;
                }

                // TODO: add sorting back in
                // figure out how to sort
                /*
                if(!empty($order = $aliasedRequest->get('order'))) {
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
                if ($resultCount > 0) {
                    $query = $query->setMaxResults($resultCount);
                }

                $vars['results'][$table . $ext] = $query->getQuery()->getResult();
            }

            $vars['allGroups'] = $orm->getRepository('StudySauceBundle:Group')->findAll();
            $serialized = serialize($searchRequest);
            $searchRequest['requestKey'] = md5($serialized);
            $this->get('cache')->save($searchRequest['requestKey'], $serialized);
            $vars['searchRequest'] = $searchRequest;

            // if request is json, merge the table fields plus a list of all the groups the user has access to
            if (in_array('application/json', $request->getAcceptableContentTypes()) || $request->get('dataType') == 'json') {
                // convert db entity to flat object
                foreach (array_merge(array_keys($searchRequest['tables']), ['allGroups']) as $table) {
                    if (!isset($vars['results'][$table])) {
                        continue;
                    }
                    $tableName = explode('-', $table)[0];
                    if ($table == 'allGroups') {
                        $tableName = 'ss_group';
                    }
                    $vars['results'][$table] = array_map(function ($e) use ($tableName, $table, $searchRequest) {
                        return self::toFirewalledEntityArray($e, [$tableName => $table == 'allGroups' ? self::$defaultTables['ss_group']
                            : $searchRequest['tables'][$tableName]]);
                    }, $vars['results'][$table]);
                }
                return new JsonResponse($vars);
            }

            return $this->render('AdminBundle:Admin:results.html.php', $vars);
        }

        public static function toFirewalledEntityArray($e, $tables = [])
        {
            $tableName = self::getJoinTable($e);
            if (empty($tables[$tableName])) {
                $tables[$tableName] = self::$defaultTables[$tableName];
            }
            $allowedFields = self::getAllFieldNames([$tableName => self::$defaultTables[$tableName]]);
            $fields = array_intersect(self::getAllFieldNames([$tableName => $tables[$tableName]]), $allowedFields);
            if(is_array($e)) {
                $obj = ['table' => $tableName, '_tableValue' => $tableName . '-' . $e['id']];
            }
            else {
                $obj = ['table' => $tableName, '_tableValue' => $tableName . '-' . $e->getId()];
            }
            foreach ($fields as $f) {
                if(is_array($e)) {
                    if(isset($e[$f])) {
                        $obj[$f] = $e[$f];
                    }
                    continue;
                }
                if (!method_exists($e, 'get' . ucfirst($f))) {
                    continue;
                }
                if (is_object($e->{'get' . ucfirst($f)}()) && $e->{'get' . ucfirst($f)}() instanceof \DateTime) {
                    $obj[$f] = $e->{'get' . ucfirst($f)}()->format('r');
                } else {
                    $obj[$f] = $e->{'get' . ucfirst($f)}();
                }
            }
            return $obj;
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
        public function saveUserAction(Request $request)
        {

            /** @var $orm EntityManager */
            $orm = $this->get('doctrine')->getManager();

            /** @var $user User */
            $user = $this->getUser();
            if (!$user->hasRole('ROLE_ADMIN')) {
                throw new AccessDeniedHttpException();
            }

            /** @var User $u */
            /** @var $userManager UserManager */
            $userManager = $this->get('fos_user.user_manager');
            if (!empty($request->get('users'))) {
                foreach ($request->get('users') as $user) {
                    $u = $orm->getRepository('StudySauceBundle:User')->findOneBy(['id' => $user['userId']]);
                    if (empty($u)) {
                        continue;
                    }

                    if (!empty($first = $user['firstName']))
                        $u->setFirst($first);
                    if (!empty($last = $user['lastName']))
                        $u->setLast($last);
                    if (!empty($email = $user['email'])) {
                        $u->setUsername($email);
                        $u->setEmail($email);
                        $userManager->updateCanonicalFields($u);
                    }

                    // add new groups
                    $groups = $u->getGroups()->map(function (Group $g) {
                        return $g->getId();
                    })->toArray();
                    $newGroups = explode(',', $user['groups']);
                    // intersection with current groups is a removal, intersection with request is an addition
                    foreach (array_diff($groups, $newGroups) as $i => $id) {
                        $u->removeGroup($u->getGroups()->filter(function (Group $g) use ($id) {
                            return $g->getId() == $id;
                        })->first());
                    }
                    foreach (array_diff($newGroups, $groups) as $i => $id) {
                        /** @var Group $g */
                        $g = $orm->getRepository('StudySauceBundle:Group')->findOneBy(['id' => $id]);
                        if (!empty($g))
                            $u->addGroup($g);
                    }

                    // add new roles
                    $roles = $u->getRoles();
                    $newRoles = explode(',', $user['roles']);
                    // intersection with current groups is a removal, intersection with request is an addition
                    foreach (array_diff($roles, $newRoles) as $i => $role) {
                        $u->removeRole($role);
                    }
                    foreach (array_diff($newRoles, $roles) as $i => $role) {
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
        public function saveGroupAction(Request $request)
        {

            /** @var $orm EntityManager */
            $orm = $this->get('doctrine')->getManager();
            /** @var $userManager UserManager */
            $userManager = $this->get('fos_user.user_manager');

            /** @var $user User */
            $user = $this->getUser();
            if (!$user->hasRole('ROLE_ADMIN')) {
                throw new AccessDeniedHttpException();
            }

            /** @var Group $g */
            if (empty($request->get('groupId'))) {
                $g = new Group();
            } else {
                $g = $orm->getRepository('StudySauceBundle:Group')->findOneBy(['id' => $request->get('groupId')]);
            }

            if (!empty($request->get('upload'))) {
                $logo = $user->getFiles()->filter(function (File $f) use ($request) {
                    return $f->getUrl() == $request->get('upload');
                })->first();
                $g->setLogo(empty($logo) ? null : $logo);
            }

            if (!empty($request->get('parent'))) {
                /** @var Group $parent */
                $parent = $orm->getRepository('StudySauceBundle:Group')->findOneBy(['id' => $request->get('parent')]);
                if (!empty($parent) && $parent == $g) {
                    $parent->removeSubgroup($g);
                    $g->setParent(null);
                } else if (!empty($parent)) {
                    $g->setParent($parent);
                    $parent->addSubgroup($g);
                }
            }

            if (!empty($name = $request->get('groupName'))) {
                $g->setName(trim($name));
            }

            if ($request->get('description') !== false) {
                $g->setDescription(!empty($request->get('description')) ? $request->get('description') : '');
            }

            // add new roles
            if (!empty($request->get('roles'))) {
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
            if (!empty($request->get('packId')) && !empty($g->getId())) {
                $this->forward('StudySauceBundle:Packs:create', ['packId' => $request->get('packId'), 'ss_group' => $request->get('ss_group'), 'ss_user' => $request->get('ss_user'), 'publish' => $request->get('publish')]);
            } else if (empty($g->getId())) {
                $orm->persist($g);
            } else if ($request->get('remove') == 'true') {
                // remove group from users
                $invites = $orm->getRepository('StudySauceBundle:Invite')->findBy(['group' => $request->get('groupId')]);
                foreach ($invites as $i => $in) {
                    $orm->remove($in);
                }
                $coupons = $orm->getRepository('StudySauceBundle:Coupon')->findBy(['group' => $request->get('groupId')]);
                foreach ($coupons as $i => $c) {
                    /** @var Coupon $c */
                    $c->setGroup(null);
                    $orm->merge($c);
                }
                if ($g->getUsers()->count() == 0) {
                    $orm->remove($g);
                } else {
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
            } else {
                $orm->merge($g);
            }
            $orm->flush();

            if (!empty($request->get('invite'))) {
                $invites = $orm->getRepository('StudySauceBundle:Invite')->findBy(['code' => $request->get('invite')]);
                if (count($invites) == 0) {
                    $newInvite = new Invite();
                    $newInvite->setCode($request->get('invite'));
                } else {
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
                } else {
                    $orm->merge($newInvite);
                }
                $orm->flush();
            }

            $searchRequest = unserialize($this->get('cache')->fetch($request->get('requestKey')) ?: '');
            return $this->forward('AdminBundle:Admin:results', array_merge($searchRequest, [
                'edit' => false,
                'read-only' => ['ss_group'],
                'new' => false,
                'ss_group-id' => $g->getId(),
                'requestKey' => null
            ]));
        }

        /**
         * @param Request $request
         * @return \Symfony\Component\HttpFoundation\Response
         */
        public function resetUserAction(Request $request)
        {

            /** @var $userManager UserManager */
            $userManager = $this->get('fos_user.user_manager');
            /** @var $orm EntityManager */
            $orm = $this->get('doctrine')->getManager();

            /** @var $user User */
            $user = $this->getUser();
            if (!$user->hasRole('ROLE_ADMIN')) {
                throw new AccessDeniedHttpException();
            }

            /** @var User $u */
            $u = $orm->getRepository('StudySauceBundle:User')->findOneBy(['id' => $request->get('userId')]);
            if (!empty($u)) {

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

        static $forCounter = 0;

        public function templateAction(Request $request)
        {

            $parser = $this->get('templating.name_parser');
            $locator = $this->get('templating.locator');

            $names = explode(',', $request->get('name'));
            $php = '/*----------------------------------------------------------- Table of Contents -----------------------------------------------------------

        ' . implode('
        ', $names) . '

        */';
            foreach ($names as $name) {
                $path = $locator->locate($parser->parse('AdminBundle:Admin:' . $name . '.html.php'));
                $file = file_get_contents($path);
                $file = preg_replace_callback('/\?>([\s\S]*?)(<\?php|$)/i', function ($match) {
                    return 'print(\'' . preg_replace('/\n/', "' + \"\\n\"\n + '", $match[1]) . '\');';
                }, '?>' . $file);
                $file = preg_replace('/use [a-z\\\\\/\s]*;/i', '', $file);
                $file = preg_replace('/->/i', '.', $file);
                $file = preg_replace('/::/i', '.', $file);
                $file = preg_replace('/\(array\)\(new stdClass\(\)\)/i', '{}', $file);
                $file = preg_replace_callback('/foreach\s*\((.*?)\s*as\s*([^\s]*)\s*(=>\s*([^\s]*)\s*)?\)\s*\{/i', function ($match) {
                    self::$forCounter++;
                    $key = !empty($match[3]) ? $match[2] : ('$for___' . self::$forCounter);
                    $name = !empty($match[3]) ? $match[4] : $match[2];
                    return 'for (' . $key . ' in ' . $match[1] . ') {
                    if(!' . $match[1] . '.hasOwnProperty(' . $key . ')) { continue; }
                    ' . $name . ' = ' . $match[1] . '[' . $key . '];';
                }, $file);
                $file = preg_replace('/\$/i', '__vars.', $file);
                preg_replace_callback('#(?=(\[(?>[^\[\]]|(?1))*+\]))#', function (&$match) use (&$file) {
                    if (strpos($match[1], '=>') !== false) {
                        $file = str_replace($match[1], '{' . trim(str_replace('=>', ':', $match[1]), " \t\n\r\0\x0B" . '[]') . '}', $file);
                    }
                    return $match[0];
                }, $file);
                $functionName = preg_replace('/[^a-z0-9_]/i', '_', $name);
                $file = '

//-----------------------------------------------------------' . $functionName . '-----------------------------------------------------------

window.views[\'' . $functionName . '\'] = ( function ' . $functionName . ' (__vars) {' . $file . '});

            ';
                $php .= $file;
            }

            $miniTables = json_encode(self::$defaultMiniTables);
            $js = <<< EOJS
(function (jQuery) {
// ^ scope for functions below, so we don't override anything

// TODO port entities here
window.AdminController = {};
window.AdminController.toFirewalledEntityArray = function (e) { return e; };
window.AdminController.__vars = {};
window.AdminController.__vars.radioCounter = 100000000;
window.AdminController.__vars.defaultMiniTables = JSON.parse('$miniTables');
window.AdminController.getAllFieldNames = function (tables) { return window.getAllFields(tables); };

var trim = function (str) {return (str || '').trim();};
var explode = function (del, str) {return (str || '').split(del);};
var array_splice = function (arr, start, length) {return (arr || []).splice(start, length);};
var array_search = function (item, arr) { var index = (arr || []).indexOf(item); return index == -1 ? false : index; };
var count = function (arr) { return (arr || []).length; };
var in_array = function (needle, arr) { return (arr || []).indexOf(needle) > -1; };
var array_values = function (arr) { return (arr || []).slice(0); };
var is_array = function (obj) { return typeof obj == 'array' || typeof obj == 'object'; }; // PHP and javascript don't make a distinction between arrays and objects syntax wise using [property], all php objects should be restful anyways
var array_keys = function (obj) {var result=[]; for (var k in obj) { if (obj.hasOwnProperty(k)) { result[result.length] = k } } return result; };
var implode = function (sep, arr) {return (arr || []).join(sep);};
var preg_replace = function (needle, replacement, subject) {debugger; return (subject || '').replace(new RegExp(needle.split('/').slice(1, -1).join('/'), needle.split('/').slice(-1)[0]), replacement);};
var ucfirst = function (str) {return (str || '').substr(0, 1).toUpperCase() + str.substr(1);};
var str_replace = function (needle, replacement, haystack) {return (haystack || '').replace(new RegExp(RegExp.escape(needle), 'g'), replacement);};
var call_user_func_array = function (context, params) {return context[context[1]].apply(context[0], params);};
var concat = function () { var str = ''; for(var a = 0; a < arguments.length; a++) { str += arguments[a]; } return str; };
var print = function (s) { window.views.__output += s };
var strtolower = function(s) { return s.toLowerCase(); };
var empty = function(s) { return typeof s == 'undefined' || ('' + s).trim() == '' || s == false || s == null; };
var json_encode = JSON.stringify;
var method_exists = function (s,m) { return typeof s == 'object' && typeof s[m] == 'function'; };
var isset = function (s) { return typeof s != 'undefined'; };

$php

})(jQuery);
EOJS;
            return new Response($js, 200, ['Content-Type' => 'text/javascript']);
        }

        /**
         * @param Request $request
         * @return \Symfony\Component\HttpFoundation\Response
         */
        public function cancelUserAction(Request $request)
        {

            /** @var $orm EntityManager */
            $orm = $this->get('doctrine')->getManager();

            /** @var $user User */
            $user = $this->getUser();
            if (!$user->hasRole('ROLE_ADMIN')) {
                throw new AccessDeniedHttpException();
            }

            /** @var User $u */
            $u = $orm->getRepository('StudySauceBundle:User')->findOneBy(['id' => $request->get('userId')]);
            if (!empty($u)) {
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
        public function removeUserAction(Request $request)
        {

            /** @var $orm EntityManager */
            $orm = $this->get('doctrine')->getManager();

            /** @var $user User */
            $user = $this->getUser();
            if (!$user->hasRole('ROLE_ADMIN')) {
                throw new AccessDeniedHttpException();
            }

            /** @var User $u */
            $u = $orm->getRepository('StudySauceBundle:User')->findOneBy(['id' => $request->get('userId')]);
            if (!empty($u)) {
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
                foreach ($u->getFiles()->toArray() as $i => $f) {
                    $u->removeFile($f);
                    $orm->remove($f);
                }
                foreach ($u->getGroups()->toArray() as $i => $gr) {
                    $u->removeGroup($gr);
                }
                foreach ($u->getInvites()->toArray() as $i => $gri) {
                    $u->removeInvite($gri);
                    $orm->remove($gri);
                }
                foreach ($u->getPayments()->toArray() as $i => $pay) {
                    $u->removePayment($pay);
                    $orm->remove($pay);
                }
                foreach ($u->getUserPacks()->toArray() as $i => $s) {
                    /** @var UserPack $s */
                    $u->removeUserPack($s);
                    $orm->remove($s);
                }
                $orm->flush();
                foreach ($u->getInvitees()->toArray() as $i => $ig) {
                    $u->removeInvitee($ig);
                    $orm->remove($ig);
                }
                $orm->remove($u);
                $orm->flush();
            }

            return $this->indexAction($request);
        }
    }
}