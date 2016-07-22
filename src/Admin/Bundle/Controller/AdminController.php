<?php

namespace Admin\Bundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use ReflectionClass;
use ReflectionParameter;
use StudySauce\Bundle\Controller\EmailsController as StudySauceEmails;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        'ss_user' => ['id' => ['id'], 'name' => ['first', 'last', 'email', 'lastVisit'], 'groups', 'packs' => ['authored', 'userPacks'], 'roles', 'actions' => ['deleted', 'invites', 'invitees']],
        'ss_group' => ['id' => ['id'], 'name' => ['name', 'logo'], 'parent' => ['parent', 'subgroups'], 'invites', 'packs' => ['packs', 'groupPacks', 'users'], 'actions' => ['deleted']],
        'pack' => ['id' => ['id'], 'name' => ['title', 'logo'], 'status', ['cards', 'group', 'groups', 'user', 'users', 'userPacks', 'userPacks.user', 'cardCount'], 'properties', 'actions'],
        'card' => ['id' => ['id'], 'name' => ['type', 'upload', 'content'], 'correct' => ['correct', 'answers', 'responseContent', 'responseType'], ['pack'], 'actions' => ['deleted']],
        'invite' => ['id' => ['code'], 'name' => ['first', 'last', 'email', 'created', 'invitee', 'user'], 'actions' => ['deleted', 'group', 'properties']],
        'user_pack' => ['id' => ['user', 'pack'], 'removed', 'downloaded', 'retention'],
        'file' => ['id' => ['url']],
        'coupon' => ['id' => ['id', 'name', 'description', 'packs', 'options']],
        'answer' => ['id' => ['value', 'card'], 'deleted', 'correct', 'content', 'id'],
        'payment' => ['id' => ['created', 'id'], 'user', 'coupons']
        // TODO: this really generalized template
        //'invite' => ['id', 'code', 'groups', 'users', 'properties', 'actions']
    ];

    public static $defaultMiniTables = [
        'pack' => ['title', 'id', 'status'],
        'ss_user' => ['first', 'last', 'email', 'id', 'deleted'],
        'ss_group' => ['name', 'id', 'deleted'],
        'file' => ['id', 'url', 'user', 'deleted']
    ];

    public static $defaultSearch = ['tables' => ['ss_user', 'ss_group'], 'invite-deleted' => false, 'user_pack-removed' => false, 'ss_user-enabled' => true, 'ss_group-deleted' => false, 'parent-ss_group-deleted' => null, 'pack-status' => '!DELETED', 'card-deleted' => false];

    public static function createEntity($table)
    {
        $class = AdminController::$allTables[$table]->name;
        return $entity = new $class();
    }

    static function makeID()
    {
        $text = "";
        $possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        for($i=0; $i < 8; $i++)
            $text .= $possible[rand(0, strlen($possible) - 1)];

        return $text;
    }

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
        $result = '';
        $joinFields = explode('.', $field);
        $lastPart = 0;
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
                    $qb = $qb->leftJoin($joinName . '.' . $jf, $newName)->select($newName);
                    // allow searching of connected fields like userPacks-removed = false
                    if (in_array($joinTable, array_keys($request['tables'])) && $lastPart < count($joinFields) - 1) {
                        $result .= (!empty($result) ? ' AND ' : '') . self::searchBuilder($qb, $joinTable, $newName, $request, $joins);
                    }
                }
                $joinName = $newName;
                $lastPart += 1;
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

    public static function sortByFields(&$arr, $fields) {
        usort($arr, function ($p1, $p2) use ($fields) {
            return strcmp(
                $p1->{'get' . ucfirst($fields[0])}() . (count($fields) > 1 ? (' ' . $p1->{'get' . ucfirst($fields[1])}()) : ''),
                $p2->{'get' . ucfirst($fields[0])}() . (count($fields) > 1 ? (' ' . $p2->{'get' . ucfirst($fields[1])}()) : '')
            );
        });
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
                        // remove table prefix from matching field, match fields line parent-ss_group-id
                        $joinRequest = array_merge([], $request);
                        foreach ($request as $j => $r) {
                            if (substr($j, 0, strlen($field) + 1) == $field . '-' && strpos($j, '-', strlen($field) + 1) !== false) {
                                $joinRequest[substr($j, strlen($field) + 1)] = $r;
                            }
                        }
                        $join = self::joinBuilder($qb, $table, $tableName, $field, $joinRequest, $joins);
                        if (!empty($join)) {
                            $where['join'] = (empty($where['join']) ? '' : ($where['join'] . ' AND ')) . $join;
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

    public static function setUpClasses(EntityManager $orm) {

        if(empty(self::$allTableClasses)) {
            self::$allTableClasses = $orm->getConfiguration()->getMetadataDriverImpl()->getAllClassNames();

            self::$allTableMetadata = array_map(function ($table) use ($orm) {
                return $orm->getMetadataFactory()->getMetadataFor($table);
            }, self::$allTableClasses);

            self::$allTables = array_combine(array_map(function (ClassMetadata $md) {
                return $md->getTableName();
            }, self::$allTableMetadata), self::$allTableMetadata);
        }
    }

    /**
     * @param $table
     * @param QueryBuilder $qb
     * @param array $joins
     * @param User $user
     * @return QueryBuilder
     * @throws Query\QueryException
     */
    public static function firewallCollection($table, QueryBuilder $qb, $joins = [], User $user) {
        if($table == 'ss_user') {
            if(!$user->hasRole('ROLE_ADMIN')) {
                if(!in_array('ss_user_inviteesinvite', $joins)) {
                    $qb = $qb->leftJoin('ss_user.invitees', 'ss_user_inviteesinvite');
                }
                $qb->andWhere('ss_user_inviteesinvite.user=:current_user OR ss_user=:current_user')
                    ->setParameter('current_user', $user);
                return $qb;
            }
        }
        else if ($table == 'ss_group') {
            if(!$user->hasRole('ROLE_ADMIN')) {
                if(!in_array('ss_group_usersss_user', $joins)) {
                    $qb = $qb->leftJoin('ss_group.users', 'ss_group_usersss_user');
                }
                if(!in_array('ss_group_parentss_group', $joins)) {
                    $qb = $qb->leftJoin('ss_group.parent', 'ss_group_parentss_group');
                }
                if(!in_array('ss_group_parentss_group_usersss_user', $joins)) {
                    $qb = $qb->leftJoin('ss_group_parentss_group.users', 'ss_group_parentss_group_usersss_user');
                }
                $qb = $qb
                    ->andWhere('ss_group_usersss_user=:current_user OR ss_group_parentss_group_usersss_user=:current_user')
                    ->setParameter('current_user', $user);
                return $qb;
            }
        }
        else if ($table == 'pack') {
            if(!in_array('pack_userPacksuser_pack', $joins)) {
                $qb = $qb->leftJoin('pack.userPacks', 'pack_userPacksuser_pack');
            }
            if(!in_array('pack_userPacksuser_pack_userss_user', $joins)) {
                $qb = $qb->leftJoin('pack_userPacksuser_pack.user', 'pack_userPacksuser_pack_userss_user');
            }
            if(!in_array('pack_userPacksuser_pack_userss_user_inviteesinvite', $joins)) {
                $qb = $qb->leftJoin('pack_userPacksuser_pack_userss_user.invitees', 'pack_userPacksuser_pack_userss_user_inviteesinvite');
            }
            if(!in_array('pack_groupsss_group', $joins)) {
                $qb = $qb->leftJoin('pack.groups', 'pack_groupsss_group');
            }
            if(!in_array('pack_groupsss_group_usersss_user', $joins)) {
                $qb = $qb->leftJoin('pack_groupsss_group.users', 'pack_groupsss_group_usersss_user');
            }
            if(!in_array('pack_groupsss_group_usersss_user_inviteesinvite', $joins)) {
                $qb = $qb->leftJoin('pack_groupsss_group_usersss_user.invitees', 'pack_groupsss_group_usersss_user_inviteesinvite');
            }
            if(!in_array('pack_groupsss_group_usersss_user_inviteesinvite_userss_user', $joins)) {
                $qb = $qb->leftJoin('pack_groupsss_group_usersss_user_inviteesinvite.user', 'pack_groupsss_group_usersss_user_inviteesinvite_userss_user');
            }
            if(!in_array('pack_groupsss_group_usersss_user_paymentspayment', $joins)) {
                $qb = $qb->leftJoin('pack_groupsss_group_usersss_user.payments', 'pack_groupsss_group_usersss_user_paymentspayment');
            }
            if(!in_array('pack_groupsss_group_usersss_user_inviteesinvite_userss_user_paymentspayment', $joins)) {
                $qb = $qb->leftJoin('pack_groupsss_group_usersss_user_inviteesinvite_userss_user.payments', 'pack_groupsss_group_usersss_user_inviteesinvite_userss_user_paymentspayment');
            }
            if(!in_array('pack_groupsss_group_usersss_user_paymentspayment_couponscoupon', $joins)) {
                $qb = $qb->leftJoin('pack_groupsss_group_usersss_user_paymentspayment.coupons', 'pack_groupsss_group_usersss_user_paymentspayment_couponscoupon');
            }
            if(!in_array('pack_groupsss_group_usersss_user_inviteesinvite_userss_user_paymentspayment_couponscoupon', $joins)) {
                $qb = $qb->leftJoin('pack_groupsss_group_usersss_user_inviteesinvite_userss_user_paymentspayment.coupons', 'pack_groupsss_group_usersss_user_inviteesinvite_userss_user_paymentspayment_couponscoupon');
            }
            if(!in_array('pack_groupsss_group_usersss_user_paymentspayment_couponscoupon_packspack', $joins)) {
                $qb = $qb->leftJoin('pack_groupsss_group_usersss_user_paymentspayment_couponscoupon.packs', 'pack_groupsss_group_usersss_user_paymentspayment_couponscoupon_packspack');
            }
            if(!in_array('pack_groupsss_group_usersss_user_inviteesinvite_userss_user_paymentspayment_couponscoupon_packspack', $joins)) {
                $qb = $qb->leftJoin('pack_groupsss_group_usersss_user_inviteesinvite_userss_user_paymentspayment_couponscoupon.packs', 'pack_groupsss_group_usersss_user_inviteesinvite_userss_user_paymentspayment_couponscoupon_packspack');
            }
            $qb = $qb
                ->andWhere('pack_userPacksuser_pack.user=:current_user
                OR pack_userPacksuser_pack_userss_user_inviteesinvite.user=:current_user
                OR (pack_groupsss_group_usersss_user=:current_user AND (pack.status=\'PUBLIC\' || pack.status=\'GROUP\' AND pack_groupsss_group_usersss_user_paymentspayment_couponscoupon_packspack=pack))
                OR (pack_groupsss_group_usersss_user_inviteesinvite.user=:current_user AND (pack.status=\'PUBLIC\' || pack.status=\'GROUP\' AND pack_groupsss_group_usersss_user_paymentspayment_couponscoupon_packspack=pack))')
                ->setParameter('current_user', $user);
            return $qb;
        }
        return $qb;
    }

    public function resultsAction(Request $request)
    {
        set_time_limit(0);
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var $user User */
        $user = $this->getUser();

        if (empty($user)) {
            throw new AccessDeniedHttpException();
        }

        self::setUpClasses($orm);

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
                if(!isset($searchRequest['cells'][$table])) {
                    $searchRequest['cells'][$table] = array_keys($tblList[$table]);
                }
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
            $ext = implode('-', array_slice($tableParts, 1));
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
            // TODO: call collection firewall
            $qb = self::firewallCollection($table, $qb, $joins, $user);

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
            if (isset($t['id']) && is_array($t['id']) && !empty($t['id']) && in_array($t['id'][0], $allFields)) {
                $qb = $qb->orderBy($table . '.' . $t['id'][0], 'DESC');
            }
            $query = $qb->setFirstResult($resultOffset);
            if ($resultCount > 0) {
                $query = $query->setMaxResults($resultCount);
            }

            $vars['results'][$table . $ext] = $query->getQuery()->getResult();
        }

        $vars['results']['allGroups'] = $orm->getRepository('StudySauceBundle:Group')->findAll();
        $serialized = serialize($searchRequest);
        $searchRequest['requestKey'] = md5($serialized);
        $this->get('cache')->save($searchRequest['requestKey'], $serialized);
        $vars['request'] = $searchRequest;

        // if request is json, merge the table fields plus a list of all the groups the user has access to
        // convert db entity to flat object
        $tableKeys = array_merge(array_keys($searchRequest['tables']), ['allGroups']);
        //$tableKeys = array_merge(in_array('application/json', $request->getAcceptableContentTypes()) ? array_keys($searchRequest['tables']) : [], ['allGroups']);
        foreach ($tableKeys as $table) {
            if (!isset($vars['results'][$table])) {
                continue;
            }
            $tableName = explode('-', $table)[0];
            if ($table == 'allGroups') {
                $tableName = 'ss_group';
            }
            $vars['resultsJSON'][$table] = array_map(function ($e) use ($tableName, $table, $searchRequest) {
                return self::toFirewalledEntityArray($e, $searchRequest['tables'], $table == 'allGroups' ? 1 : 3);
            }, $vars['results'][$table]);
        }
        if (in_array('application/json', $request->getAcceptableContentTypes()) || $request->get('dataType') == 'json') {
            $vars['results'] = $vars['resultsJSON'];
            unset($vars['resultsJSON']);
            return new JsonResponse($vars);
        }

        return $this->render('AdminBundle:Admin:results.html.php', $vars);
    }

    // $levels helps stop recursion, this number should not be high and it should not be override by anyone else
    public static function toFirewalledEntityArray($e, $tables = [], $levels = 3)
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
            $obj = ['table' => $tableName, '_tableValue' => $tableName . '-' . (method_exists($e, 'getId') ? $e->getId() : '')];
            // return a newKey to pair up with some randomized value generated from the client,
            // so we make sure we get all the right edit rows accounted for in-case it has changed while we were saving.
            if(isset($e->newId)) {
                $obj['newId'] = $e->newId;
            }
        }
        foreach ($fields as $f) {
            if (is_array($e)) {
                if (isset($e[$f])) {
                    $obj[$f] = $e[$f];
                }
                continue;
            }
            if (!method_exists($e, 'get' . ucfirst($f))) {
                continue;
            }
            $value = $e->{'get' . ucfirst($f)}();
            if (is_array($value)) {
                array_walk_recursive ($value, function(&$v) {
                    if($v instanceof \DateTime) {
                        $v = $v->format('r');
                    }
                });
                $obj[$f] = $value;
            } else if (is_object($value) && $value instanceof \DateTime) {
                $obj[$f] = $value->format('r');
            } else if (isset($tables[$tableName]) && isset(self::$allTables[$tableName]->getAssociationMappings()[$f])) {
                $association = self::$allTables[$tableName]->getAssociationMappings()[$f];
                $tableIndex = array_search($association['targetEntity'], self::$allTableClasses);
                $joinTable = array_keys(self::$allTables)[$tableIndex];
                if (isset($tables[$joinTable]) && !empty($value) && $levels > 0) {
                    if ($value instanceof Collection) {
                        if (!isset($obj[$f])) {
                            $obj[$f] = [];
                        }
                        foreach ($value->toArray() as $subE) {
                            $obj[$f][] = self::toFirewalledEntityArray($subE, $levels - 1 == 0 ? [$joinTable => $tables[$joinTable]] : $tables, $levels - 1);
                        }
                    } else {
                        $obj[$f] = self::toFirewalledEntityArray($value, $levels - 1 == 0 ? [$joinTable => $tables[$joinTable]] : $tables, $levels - 1);
                    }
                }
            } else {
                $obj[$f] = $value;
            }
        }
        return $obj;
    }

    public function saveAction(Request $request) {

        self::standardSave($request, $this->container);

        if(!empty($url = $request->get('redirect'))) {
            return $this->redirect($url);
        }

        $searchRequest = unserialize($this->get('cache')->fetch($request->get('requestKey')) ?: 'a:0:{};');
        return $this->forward('AdminBundle:Admin:results', $searchRequest);
    }

    /**
     * @param Request $request
     * @param ContainerInterface $container
     * @return array
     * @throws \Exception
     */
    public static function standardSave(Request $request, ContainerInterface $container) {
        /** @var $orm EntityManager */
        $orm = $container->get('doctrine')->getManager();

        self::setUpClasses($orm);

        if(!empty($request->get('requestKey'))) {
            $searchRequest = unserialize($container->get('cache')->fetch($request->get('requestKey')) ?: 'a:0:{};');
            $tables = $searchRequest['tables'];
        }
        if(!empty($request->get('tables'))) {
            $tables = $request->get('tables');
        }
        if(empty($tables)) {
            throw new \Exception('Don\'t know what to save!');
        }

        $results = [];

        foreach($tables as $tableName => $fields) {

            if (!empty($entities = $request->get($tableName))) {

                if(!isset($entities[0])) {
                    $entities = [$entities];
                }

                foreach($entities as $e) {
                    $class = AdminController::$allTables[$tableName]->name;
                    $entity = self::applyFields($class, $tableName, $fields, $e, $orm);

                    if(empty($entity->getId())) {
                        $orm->persist($entity);
                    }
                    else {
                        $orm->merge($entity);
                    }
                    $orm->flush();
                    $results[] = $entity;
                }
            }
        }
        return $results;
    }

    /**
     * @param $class
     * @param $tableName
     * @param $fields
     * @param $e
     * @param EntityManager $orm
     * @return null|object
     * @throws \Exception
     */
    public static function applyFields($class, $tableName, $fields, $e, EntityManager $orm) {
        $allFields = self::getAllFieldNames([$tableName => $fields]);
        if($e instanceof $class) {
            $entity = $e;
            $e = (array)$e;
        }
        else if(is_array($e) && empty($e['id'])) {
            $entity = new $class;
            $entity->newId = isset($e['newId']) ? $e['newId'] : ' ';
        }
        else {
            $hasId = true;
            $query = $orm->getRepository($class)->createQueryBuilder($tableName);
            foreach(self::$defaultTables[$tableName]['id'] as $f) {
                // select other parts of the ID to help with adding and removing remove lists by ID
                $other = array_values(array_filter(self::$defaultTables[$tableName]['id'], function ($f) use ($e) {return !isset($e[$f]);}));
                // TODO: is this an edge case?
                if (is_array($e) && count($other) && isset($e['id'])) {
                    $query = $query->andWhere($tableName . '.' . $other[0] . '=:' . $other[0])
                        ->setParameter($other[0], $e['id']);
                    $e[$other[0]] = $e['id'];
                    unset($e['id']);
                }
                else if (in_array($f, self::$allTables[$tableName]->identifier)) {
                    $query = $query->andWhere($tableName . '.' . $f . '=:' . $f)
                        ->setParameter($f, is_array($e) ? $e[$f] : $e);
                }
                else if (isset($e[$f]) && (!is_object($e[$f]) || !isset($e[$f]->newId))) {
                    $query = $query->andWhere($tableName . '.' . $f . '=:' . $f)
                        ->setParameter($f, is_array($e) ? $e[$f] : $e);
                }
                else {
                    $hasId = false;
                    break;
                }
            }
            if($hasId) {
                $entity = $query->getQuery()->setMaxResults(1)->getOneOrNullResult();
            }
            if(empty($entity)) {
                $entity = new $class;
                $entity->newId = isset($e['newId']) ? $e['newId'] : ' ';
            }
        }

        foreach($allFields as $f) {

            if(isset($e[$f])) { // only apply fields that are set, removes have to be set to 'remove' => 'true'

                if(isset(self::$allTables[$tableName]->associationMappings[$f])) {
                    $association = self::$allTables[$tableName]->associationMappings[$f];
                    $tableIndex = array_search($association['targetEntity'], self::$allTableClasses);
                    $joinTable = array_keys(self::$allTables)[$tableIndex];
                    if($association['type'] == ClassMetadataInfo::ONE_TO_ONE || $association['type'] == ClassMetadataInfo::MANY_TO_ONE) {
                        // create entities from array using same assignment functions as here
                        $value = $e[$f];
                        if(!empty($e[$f])) {
                            if(is_object($value) && (new ReflectionClass($value))->getNamespaceName() == 'StudySauce\\Bundle\\Entity') {
                                // nothing needed to do because it already the entity we are looking for
                            }
                            else {
                                if(!is_array($value)) {
                                    $value = ['id' => $value];
                                }
                                $value = self::applyFields($association['targetEntity'], $joinTable, self::$defaultTables[$joinTable], $value, $orm);
                            }
                        }
                        else {
                            $value = null;
                        }
                        // many to one, just lookup object and call set property normally
                        if(($type = self::parameterType('set' . ucfirst($f), $entity)) !== false) {
                            call_user_func_array([$entity, 'set' . ucfirst($f)], [$value]);
                            if (!empty($value->newId)) {
                                $orm->persist($value);
                            } else if(!empty($value)) {
                                $orm->merge($value);
                            }
                        }
                    }
                    // one to many, look for add function instead, remove ending s from field name like addGroupPack
                    else {
                        $entities = $e[$f];
                        if(!is_array($entities) || !isset($entities[0])) {
                            $entities = [$entities];
                        }

                        // if dealing with multiple entities, perform actions for each
                        $removeAnswers = [];
                        foreach($entities as $subE) {
                            if(empty($subE)) {
                                continue;
                            }
                            // TODO: put this in a shared model between client and server where setting a _clear answer resets the list?  Maybe there is a command before a row is saved to remove the old answers?

                            if($subE == '_clear' && method_exists($entity, 'get' . ucfirst($f))) {
                                $removeAnswers = call_user_func_array([$entity, 'get' . ucfirst($f)], []);
                                if($removeAnswers instanceof Collection) {
                                    $removeAnswers = $removeAnswers->toArray();
                                }
                                continue;
                            }
                            // automatically do inverse mapping
                            $joinFields = self::$defaultTables[$joinTable]; //[$joinTable => array_keys($subE)];
                            $isAdding = true;
                            if(!is_array($subE)) {
                                $subE = ['id' => $subE];
                            }
                            if(isset($association['mappedBy'])) {
                                // TODO: set to entity or set to null if removing a many-to-one
                                $isAdding = !isset($subE['remove']) || $subE['remove'] !== 'true';
                                if($association['type'] == ClassMetadataInfo::ONE_TO_MANY) {
                                    $subE = array_merge([$association['mappedBy'] => $entity], $subE);
                                    $joinFields = array_merge($joinFields, [$association['mappedBy']]);
                                }
                            }
                            // TODO: add entity[clear] = 'true' to clear lists to support deleting and disassociating, maybe has to submit has of all IDs for safety?
                            $childEntity = self::applyFields($association['targetEntity'], $joinTable, $joinFields, $subE, $orm);
                            if(isset($e['remove']) && $e['remove'] === 'true') {
                                $isAdding = false;
                            }
                            if(($type = self::parameterType($method = (!$isAdding ? 'remove' : 'add') . ucfirst(rtrim($f, 's')), $entity)) !== false) {
                                $parameters = [];
                                if(!empty($childEntity)) {
                                    $parameters[] = &$childEntity;
                                }
                                else {
                                    $parameters = [null];
                                }
                                // TODO: ? copy back from parameters to childEntity?
                                call_user_func_array([$entity, $method], $parameters);
                                if($isAdding && ($unsetI = array_search($childEntity, $removeAnswers)) !== false) {
                                    array_splice($removeAnswers, $unsetI, 1); // remove from remove array because we are keeping it, in the case of answers, the text is looked up by value so existing answers will match new list
                                }
                                if (!empty($childEntity->newId)) {
                                    $orm->persist($childEntity);
                                } else if(!empty($value)) {
                                    $orm->merge($childEntity);
                                }
                            }
                        }

                        // if we are clearing, remove the entities that were not added in the complete list above
                        if(($type = self::parameterType($method = 'remove' . ucfirst(rtrim($f, 's')), $entity)) !== false) {
                            foreach($removeAnswers as $a) {
                                call_user_func_array([$entity, $method], [$a]);
                                $orm->remove($a);
                            }
                        }
                    }
                }
                else if(($rp = self::parameterType('set' . ucfirst($f), $entity)) !== false) {
                    $type = $rp->getClass();
                    $value = $e[$f];
                    if(is_object($type) && $type->getNamespaceName() == 'StudySauce\\Bundle\\Entity') {
                        // TODO: this might not work because it should have to use association mappings above
                        $tableIndex = array_search($type->getName(), self::$allTableClasses);
                        $joinTable = array_keys(self::$allTables)[$tableIndex];
                        if(!is_array($value)) {
                            $value = ['id' => $value];
                        }
                        $joinFields = self::$defaultTables[$joinTable];
                        $value = self::applyFields($type->getName(), $joinTable, $joinFields, $value, $orm);
                        $parameters = [];
                        if(!empty($value)) {
                            $parameters[] = &$value;
                        }
                        else {
                            $parameters = [null];
                        }
                        call_user_func_array([$entity, 'set' . ucfirst($f)], $parameters);
                        if (!empty($value->newId)) {
                            $orm->persist($value);
                        } else if(!empty($value)) {
                            $orm->merge($value);
                        }
                    }
                    // TODO: handle roles, properties and datetime data types, NULL to clear a field
                    else if (is_object($type) && $type->getName() == '\\DateTime') {
                        $value = new \DateTime($value);
                        call_user_func_array([$entity, 'set' . ucfirst($f)], [!empty($value) ? $value : null]);
                    }
                    else {
                        call_user_func_array([$entity, 'set' . ucfirst($f)], [$value]);
                    }
                }
            }
        }

        return $entity;
    }

    /**
     * @param $method
     * @param $entity
     * @return bool|ReflectionParameter
     */
    public static function parameterType($method, $entity) {
        if(method_exists($entity, $method)) {
            $class = new ReflectionClass($entity);
            $method = $class->getMethod($method);
            $params = $method->getParameters();
            return $params[0];
        }
        return false;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function saveGroupAction(Request $request)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var $user User */
        $user = $this->getUser();
        if (!$user->hasRole('ROLE_ADMIN')) {
            throw new AccessDeniedHttpException();
        }
        $allGroups = $orm->getRepository('StudySauceBundle:Group')->findAll();

        /** @var Group $g */
        list($g) = self::standardSave($request, $this->container);

        $searchRequest = unserialize($this->get('cache')->fetch($request->get('requestKey')) ?: 'a:0:{};');

        if (!empty($request->get('ss_group')) && is_array($request->get('ss_group'))) {
            if(isset($request->get('ss_group')['deleted']) && $request->get('ss_group')['deleted'] == '1') {
                $g->setName($g->getName() . '-Deleted On ' . time());
                $orm->merge($g);
                $orm->flush();
                return $this->redirect($this->generateUrl('groups'));
            }
            // if the ID was empty, update the results request with the new ID
            if(isset($request->get('ss_group')['id']) && empty($request->get('ss_group')['id'])) {
                $searchRequest['edit'] = false;
                $searchRequest['read-only'] = ['ss_group'];
                $searchRequest['new'] = false;
                $searchRequest['ss_group-id'] = $g->getId();
                $searchRequest['requestKey'] = null;
            }
            // TODO: generalize this recursive stuff, maybe if parent_path can be used to lookup all entities and perform the same action on each
            if(isset($request->get('ss_group')['groupPacks'])) {
                $subGroups = [$g->getId()];
                $added = true;
                while($added) {
                    $added = false;
                    foreach($allGroups as $subGroup) {
                        /** @var Group $subGroup */
                        if(!empty($subGroup->getParent())
                            && in_array($subGroup->getParent()->getId(), $subGroups)
                            && !in_array($subGroup->getId(), $subGroups)) {
                            $subGroups[count($subGroups)] = $subGroup->getId();

                            $entity = self::applyFields(AdminController::$allTables['ss_group']->name, 'ss_group', ['groupPacks'], array_merge($request->get('ss_group'), ['id' => $subGroup->getId()]), $orm);

                            if(empty($entity->getId())) {
                                $orm->persist($entity);
                            }
                            else {
                                $orm->merge($entity);
                            }
                            $orm->flush();
                            $added = true;
                        }
                    }
                }
            }
        }

        if(!empty($request->get('pack')) && is_array($request->get('pack'))) {
            if(isset($request->get('pack')['groups'])) {
                foreach($request->get('pack')['groups'] as $subG) {
                    $subGroups = [$subG['id']];
                    $added = true;
                    while($added) {
                        $added = false;
                        foreach($allGroups as $subGroup) {
                            /** @var Group $subGroup */
                            if(!empty($subGroup->getParent())
                                && in_array($subGroup->getParent()->getId(), $subGroups)
                                && !in_array($subGroup->getId(), $subGroups)) {
                                $subGroups[count($subGroups)] = $subGroup->getId();

                                $entity = self::applyFields(AdminController::$allTables['pack']->name, 'pack', ['groups'], array_merge($request->get('pack'), ['groups' => [array_merge($subG, ['id' => $subGroup->getId()])]]), $orm);

                                if(empty($entity->getId())) {
                                    $orm->persist($entity);
                                }
                                else {
                                    $orm->merge($entity);
                                }
                                $orm->flush();
                                $added = true;
                            }
                        }
                    }
                }
            }
        }

        return $this->forward('AdminBundle:Admin:results', $searchRequest);
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
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        self::setUpClasses($orm);

        $parser = $this->get('templating.name_parser');
        $locator = $this->get('templating.locator');

        $toc = '/*----------------------------------------------------------- Table of Contents -----------------------------------------------------------';
        $php = '';
        $path = $locator->locate($parser->parse('AdminBundle:Admin:groups.html.php'));
        $handle = opendir(dirname($path));
        while (false !== ($name = readdir($handle))) {
            if (substr($name, strlen($name) - strlen('.html.php')) != '.html.php') {
                continue;
            }
            $name = substr($name, 0, strlen($name) - strlen('.html.php'));
            $toc .= '
    ' . $name;

            $file = file_get_contents(dirname($path) . DIRECTORY_SEPARATOR . $name . '.html.php');
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
            $innerMatches = [];
            $replacements = [];
            preg_replace_callback('#(?=(\[(?>[^\[\]]|(?1))*+\]))#', function (&$match) use (&$file, &$innerMatches, &$replacements) {
                if (strpos($match[1], '=>') !== false) {
                    $object = trim(str_replace('=>', ':', $match[1]), " \t\n\r\0\x0B");
                    $innerMatches[] = $match[1];
                    $replacements[] = '{' . substr($object, 1, strlen($object) - 2) . '}';
                    $innerMatches[] = str_replace(array_keys($innerMatches), array_values($innerMatches), $object);
                    $replacements[] = '{' . substr($object, 1, strlen($object) - 2) . '}';
                    $file = str_replace($innerMatches, $replacements, $file);
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

        $miniTables = addslashes(json_encode(self::$defaultMiniTables));
        $allTables = addslashes(json_encode(self::$allTables)); // TODO: run this through firewall
        $defaultTables = addslashes(json_encode(self::$defaultTables));
        $js = <<< EOJS
        $toc */
(function (jQuery) {
// ^ scope for functions below, so we don't override anything

// TODO port entities here
window.AdminController = {};
window.AdminController.toFirewalledEntityArray = function (e) { return e; };
window.AdminController.makeID = function ()
{
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for( var i=0; i < 8; i++ )
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
};
window.AdminController.__vars = {};
window.AdminController.__vars.radioCounter = 100000000;
window.AdminController.__vars.defaultMiniTables = JSON.parse('$miniTables');
window.AdminController.__vars.allTables = JSON.parse('$allTables');
window.AdminController.__vars.defaultTables = JSON.parse('$defaultTables');
window.AdminController.createEntity = function (t) {return $.extend({}, window.views.__defaultEntities[t]);};
window.AdminController.sortByFields = function (arr, fields) {
arr.sort(function (a, b) { return (a[fields[0]] + ' ' + a[fields[1]]).toLocaleLowerCase() > (b[fields[0]] + ' ' + b[fields[1]]).toLocaleLowerCase() }); }
window.AdminController.TableMapping = {
    getAssociationMappings: function () { return this.associationMappings; }
};
for(var t in window.AdminController.__vars.allTables) {
    if(window.AdminController.__vars.allTables.hasOwnProperty(t)) {
        window.AdminController.__vars.allTables[t] = $.extend(window.AdminController.__vars.allTables[t], window.AdminController.TableMapping);
    }
}
window.AdminController.getAllFieldNames = function (tables) { return window.getAllFieldNames(tables); };

var array_shift = function (arr) {return arr.shift();};
var strpos = function (str, match) {var i; return (i = str.indexOf(match)) > -1 ? i : false;};
var substr = function (str, start, length) {return str.substr(start, length);};
var is_numeric = function (num) {return !isNaN(parseInt(num)) || !isNaN(parseFloat(num));};
var strlen = function (str) {return (''+(str || '')).length;};
var array_merge = function () {
var isObject = typeof arguments[0] == 'object' && arguments[0] != null && arguments[0].constructor != Array;
var args = [isObject ? {} : []];
for(var a = 0; a < arguments.length; a++) {
    args[args.length] = arguments[a];
};
return args.reduce(function (a, b) {
    return isObject ? $.extend(a, b) : $.merge(a, b);
});
};
var round = function (num, digits) {
    if(digits > 0) {
        return Math.round(num * (10 * (digits ? digits : 0))) / (10 * (digits ? digits : 0));
    }
    return Math.round(num);
};
var is_string = function (obj) {return typeof obj == 'string';};
var is_a = function (obj, typeStr) { return typeof obj == 'object' && obj != null && obj.constructor.name == typeStr();};
var intval = function (str) {var result = parseInt(str); return isNaN(result) ? 0 : result;};
var trim = function (str) {return (str || '').trim();};
var explode = function (del, str) {return (str || '').split(del);};
var array_slice = function (arr, start, length) { return (arr || []).slice(start, length); };
var array_splice = function (arr, start, length) { return arr.splice(start, length); };
var array_search = function (item, arr) { var index = (arr || []).indexOf(item); return index == -1 ? false : index; };
var count = function (arr) { return (arr || []).length; };
var in_array = function (needle, arr) { return (arr || []).indexOf(needle) > -1; };
var array_values = function (arr) { return (arr || []).slice(0); };
var is_array = function (obj) { return typeof obj == 'array' || typeof obj == 'object'; }; // PHP and javascript don't make a distinction between arrays and objects syntax wise using [property], all php objects should be restful anyways
var array_keys = function (obj) {var result=[]; for (var k in obj) { if (obj.hasOwnProperty(k)) { result[result.length] = k } } return result; };
var implode = function (sep, arr) {return (arr || []).join(sep);};
var preg_replace = function (needle, replacement, subject) {
    return (subject || '').replace(new RegExp(needle.split('/').slice(1, -1).join('/'), needle.split('/').slice(-1)[0] + 'g'), replacement);
};
var number_format = function (num, digits) { return num.toFixed(digits);};
var preg_match = function (needle, subject, matches) {
    var result = (new RegExp(needle.split('/').slice(1, -1).join('/'), needle.split('/').slice(-1)[0])).exec(subject);
    if(result == null) {
        return 0;
    }
    if(typeof matches != 'undefined') {
        for(var m = 0; m < result.length; m++) {
            matches[m] = result[m];
        }
    }
    return result.length;
};
var ucfirst = function (str) {return (str || '').substr(0, 1).toLocaleUpperCase() + str.substr(1);};
var str_replace = function (needle, replacement, haystack) {return (haystack || '').replace(new RegExp(RegExp.escape(needle), 'g'), replacement);};
var call_user_func_array = function (context, params) {return context[0][context[1]].apply(context[0], params);};
var print = function (s) { window.views.__output += s };
var strtolower = function(s) { return s.toLocaleLowerCase(); };
var empty = function(s) { return typeof s == 'undefined' || ('' + s).trim() == '' || s === false || s === 'false' || s == null || (typeof s == 'object' && s.constructor == Array && s.length == 0) ; };
var json_encode = JSON.stringify;
var method_exists = function (s,m) { return typeof s == 'object' && typeof s[m] == 'function'; };
var isset = function (s) { return typeof s != 'undefined'; };

$php

})(jQuery);
EOJS;
        return new Response($js, 200, [
            'Content-Type' => 'application/javascript',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => 0
        ]);
    }
}
