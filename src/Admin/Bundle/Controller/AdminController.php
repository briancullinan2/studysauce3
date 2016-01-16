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
    private static $paidStr = '';
    /** @var ClassMetadata[] $allTables */
    private static $allTables;
    /** @var ClassMetadata[] $allTableMetadata */
    private static $allTableMetadata;
    /** @var string[] $allTableClasses */
    private static $allTableClasses;
    private static $tables;

    /**
     * @param EntityManager $orm
     * @param Request $request
     * @param $joins
     * @return QueryBuilder
     */
    static function searchBuilder(EntityManager $orm, Request $request, &$joins = [])
    {
        $joins = [];
        /** @var QueryBuilder $qb */
        $qb = $orm->getRepository('StudySauceBundle:User')->createQueryBuilder('u');

        if(empty(self::$paidStr)) {
            $paidGroups = $orm->getRepository('StudySauceBundle:Group')->createQueryBuilder('g')
                ->select('g.id')
                ->andWhere('g.roles LIKE \'%s:9:"ROLE_PAID"%\'')
                ->getQuery()
                ->getArrayResult();
            self::$paidStr = implode(', ', array_map(function ($x) { return $x['id']; }, $paidGroups));
        }

        if(!empty($lastVisit = $request->get('lastLogin'))) {
            $start = new \DateTime(explode(' - ', $lastVisit)[0]);
            if(count(explode(' - ', $lastVisit)) > 1) {
                $end = new \DateTime(explode(' - ', $lastVisit)[1]);
                $qb = $qb->andWhere('u.lastVisit >= \'' . $start->format('Y-m-d 00:00:00') . '\' AND u.lastVisit <= \'' . $end->format('Y-m-d 23:59:59') . '\'');
            }
            else {
                $qb = $qb->andWhere('u.lastVisit >= \'' . $start->format('Y-m-d 00:00:00') . '\'');
            }
        }

        if(!empty($created = $request->get('created'))) {
            $start = new \DateTime(explode(' - ', $created)[0]);
            $end = new \DateTime(explode(' - ', $created)[1]);
            $qb = $qb->andWhere('u.created >= \'' . $start->format('Y-m-d 00:00:00') . '\' AND u.created <= \'' . $end->format('Y-m-d 23:59:59') . '\'');
        }

        if(!empty($search = $request->get('search'))) {
            if(strpos($search, '%') === false) {
                $search = '%' . $search . '%';
            }
            if(!in_array('g', $joins)) {
                $qb = $qb->leftJoin('u.groups', 'g');
                $joins[] = 'g';
            }
            $qb = $qb->andWhere('u.first LIKE :search OR u.last LIKE :search OR u.email LIKE :search OR g.name LIKE :search OR g.description LIKE :search')
                ->setParameter('search', $search);
        }

        $role = $request->get('role');
        if($role != 'ROLE_GUEST') {
            $qb = $qb->andWhere('u.roles NOT LIKE \'%s:10:"ROLE_GUEST"%\'');
        }
        if($role != 'ROLE_DEMO') {
            $qb = $qb->andWhere('u.roles NOT LIKE \'%s:9:"ROLE_DEMO"%\'');
        }
        if($role == 'ROLE_STUDENT') {
            $qb = $qb->andWhere('u.roles NOT LIKE \'%s:12:"ROLE_ADVISER"%\' AND u.roles NOT LIKE \'%s:19:"ROLE_MASTER_ADVISER"%\' AND u.roles NOT LIKE \'%s:12:"ROLE_PARTNER"%\' AND u.roles NOT LIKE \'%s:11:"ROLE_PARENT"%\'');
        }
        elseif($role == 'ROLE_PAID') {
            if(!in_array('g', $joins)) {
                $qb = $qb->leftJoin('u.groups', 'g');
                $joins[] = 'g';
            }
            $qb = $qb->andWhere('u.roles LIKE \'%s:9:"ROLE_PAID"%\' OR g.id IN (' . self::$paidStr . ')');
        }
        elseif(!empty($role)) {
            $qb = $qb->andWhere('u.roles LIKE \'%s:' . strlen($role) . ':"' . $role . '"%\'');
        }

        if(!empty($group = $request->get('group'))) {
            if(!in_array('g', $joins)) {
                $qb = $qb->leftJoin('u.groups', 'g');
                $joins[] = 'g';
            }
            if($group == 'nogroup') {
                $qb = $qb->andWhere('g.id IS NULL');
            }
            else {
                $qb = $qb->andWhere('g.id=:gid')->setParameter('gid', intval($group));
            }
        }

        if(!empty($last = $request->get('last'))) {
            $qb = $qb->andWhere('u.last LIKE \'' . $last . '\'');
        }

        if(!empty($completed = $request->get('completed'))) {
            if(!in_array('c1', $joins)) {
                $qb = $qb
                    ->leftJoin('u.course1s', 'c1')
                    ->leftJoin('u.course2s', 'c2')
                    ->leftJoin('u.course3s', 'c3');
                $joins[] = 'c1';
            }
            if(($pos = strpos($completed, '1')) !== false) {
                if(substr($completed, $pos - 1, 1) != '!')
                    $qb = $qb->andWhere('c1.lesson1=4 AND c1.lesson2=4 AND c1.lesson3=4 AND c1.lesson4=4 AND c1.lesson5=4 AND c1.lesson6=4');
                else
                    $qb = $qb->andWhere('(c1.lesson1<4 OR c1.lesson1 IS NULL) OR (c1.lesson2<4 OR c1.lesson2 IS NULL) OR (c1.lesson3<4 OR c1.lesson3 IS NULL) OR (c1.lesson4<4 OR c1.lesson4 IS NULL) OR (c1.lesson5<4 OR c1.lesson5 IS NULL) OR (c1.lesson6<4 OR c1.lesson6 IS NULL)');
            }
            if(($pos = strpos($completed, '2')) !== false) {
                if(substr($completed, $pos - 1, 1) != '!')
                    $qb = $qb->andWhere('c2.lesson1=4 AND c2.lesson2=4 AND c2.lesson3=4 AND c2.lesson4=4 AND c2.lesson5=4');
                else
                    $qb = $qb->andWhere('(c2.lesson1<4 OR c2.lesson1 IS NULL) OR (c2.lesson2<4 OR c2.lesson2 IS NULL) OR (c2.lesson3<4 OR c2.lesson3 IS NULL) OR (c2.lesson4<4 OR c2.lesson4 IS NULL) OR (c2.lesson5<4 OR c2.lesson5 IS NULL)');
            }
            if(($pos = strpos($completed, '3')) !== false) {
                if(substr($completed, $pos - 1, 1) != '!')
                    $qb = $qb->andWhere('c3.lesson1=4 AND c3.lesson2=4 AND c3.lesson3=4 AND c3.lesson4=4 AND c3.lesson5=4');
                else
                    $qb = $qb->andWhere('(c3.lesson1<4 OR c3.lesson1 IS NULL) OR (c3.lesson2<4 OR c3.lesson2 IS NULL) OR (c3.lesson3<4 OR c3.lesson3 IS NULL) OR (c3.lesson4<4 OR c3.lesson4 IS NULL) OR (c3.lesson5<4 OR c3.lesson5 IS NULL)');
            }
        }


        // check for individual lesson filters
        for($i = 1; $i <= 17; $i++) {
            if(!empty($lesson = $request->get('lesson' . $i))) {
                if (!in_array('c1', $joins)) {
                    $qb = $qb
                        ->leftJoin('u.course1s', 'c1')
                        ->leftJoin('u.course2s', 'c2')
                        ->leftJoin('u.course3s', 'c3');
                    $joins[] = 'c1';
                }
                if($i > 12) {
                    $l = $i - 12;
                    $c = 3;
                }
                elseif($i > 7) {
                    $l = $i - 7;
                    $c = 2;
                }
                else {
                    $l = $i;
                    $c = 1;
                }
                if($lesson == 'yes') {
                    $qb = $qb->andWhere('c' . $c . '.lesson' . $l . '=4');
                }
                else {
                    $qb = $qb->andWhere('c' . $c . '.lesson' . $l . '<4 OR ' . 'c' . $c . '.lesson' . $l . ' IS NULL');
                }
            }
        }

        if(!empty($paid = $request->get('paid'))) {
            if(!in_array('g', $joins)) {
                $qb = $qb->leftJoin('u.groups', 'g');
                $joins[] = 'g';
            }
            if($paid == 'yes') {
                $qb = $qb->andWhere('u.roles LIKE \'%s:9:"ROLE_PAID"%\' OR g.id IN (' . self::$paidStr . ')');
            }
            else {
                $qb = $qb->andWhere('u.roles NOT LIKE \'%s:9:"ROLE_PAID"%\' AND (g IS NULL OR g.id NOT IN (' . self::$paidStr . '))');
            }
        }

        if(!empty($goals = $request->get('goals'))) {
            if(!in_array('goals', $joins)) {
                $qb = $qb->leftJoin('u.goals', 'goals');
                $joins[] = 'goals';
            }
            if($goals == 'yes') {
                $qb = $qb->andWhere('goals.id IS NOT NULL');
            }
            else {
                $qb = $qb->andWhere('goals.id IS NULL');
            }
        }

        if(!empty($deadlines = $request->get('deadlines'))) {
            if(!in_array('deadlines', $joins)) {
                $qb = $qb->leftJoin('u.deadlines', 'deadlines');
                $joins[] = 'deadlines';
            }
            if($deadlines == 'yes') {
                $qb = $qb->andWhere('deadlines.id IS NOT NULL');
            }
            else {
                $qb = $qb->andWhere('deadlines.id IS NULL');
            }
        }

        if(!empty($schedules = $request->get('schedules'))) {
            if(!in_array('schedules', $joins)) {
                $qb = $qb->leftJoin('u.schedules', 'schedules');
                $joins[] = 'schedules';
            }
            if($schedules == 'yes') {
                $qb = $qb->andWhere('schedules.university IS NOT NULL AND schedules.university!=\'\'');
            }
            else {
                $qb = $qb->andWhere('schedules.university IS NULL OR schedules.university=\'\'');
            }
        }

        if(!empty($grades = $request->get('grades'))) {
            if(!in_array('schedules', $joins)) {
                $qb = $qb->leftJoin('u.schedules', 'schedules');
                $joins[] = 'schedules';
            }
            if(!in_array('grades', $joins)) {
                $qb = $qb->leftJoin('schedules.courses', 'courses');
                $qb = $qb->leftJoin('courses.grades', 'grades');
                $joins[] = 'grades';
            }
            if($grades == 'yes') {

                $qb = $qb->andWhere('grades.assignment IS NOT NULL AND grades.assignment!=\'\'');
            }
            else {
                $qb = $qb->andWhere('grades.assignment IS NULL OR grades.assignment=\'\'');
            }
        }

        if(!empty($notes = $request->get('notes'))) {
            if($notes == 'yes') {
                $qb = $qb->andWhere('u.evernote_access_token IS NOT NULL AND u.evernote_access_token!=\'\'');
            }
            else {
                $qb = $qb->andWhere('u.evernote_access_token IS NULL OR u.evernote_access_token=\'\'');
            }
        }

        if(!empty($partners = $request->get('partners'))) {
            if(!in_array('partners', $joins)) {
                $qb = $qb->leftJoin('u.partnerInvites', 'partners');
                $joins[] = 'partners';
            }
            if($partners == 'yes') {
                $qb = $qb->andWhere('partners.id IS NOT NULL');
            }
            else {
                $qb = $qb->andWhere('partners.id IS NULL');
            }
        }

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param string $table
     * @param array $request
     * @return QueryBuilder
     */
    private function newSearchBuilder(QueryBuilder $qb, $table, $request, $joins = [])
    {
        /** @var QueryBuilder $qb $f */
        foreach(self::$tables[$table] as $f) {
            if($f == 'id') {
                // TODO: do id and activity time here
                if (isset($request['id']) && is_numeric($request['id'])) {
                    $id = intval($request['id']);
                }
                else if (isset($request['search']) && is_numeric($request['search'])) {
                    $id = intval($request['search']);
                }

                if (!empty($id)) {
                    $qb = $qb->orWhere($table . '.id = :id')
                        ->setParameter('id', $id);
                }
            }

            if ($f == 'name') {
                if (isset($request['name'])) {
                    $name = $request['name'];
                }
                else if (isset($request['search'])) {
                    $name = '%' . $request['search'] . '%';
                }

                if (!empty($name)) {
                    if ($table == 'ss_user') {
                        $qb = $qb->orWhere('ss_user.first LIKE :search OR ss_user.last LIKE :search OR ss_user.email LIKE :search')
                            ->setParameter('search', $name);
                    } else if ($table == 'ss_group') {
                        $qb = $qb->orWhere('ss_group.name LIKE :search OR ss_group.description LIKE :search')
                            ->setParameter('search', $name);
                    } else if ($table == 'pack') {
                        $qb = $qb->orWhere('pack.title LIKE :search')
                            ->setParameter('search', $name);
                    }
                }
            }

            $associated = self::$allTables[$table]->getAssociationMappings();
            if (isset($associated[$f])) {
                if (isset($request[$f])) {
                    $joinSearch = $request[$f];
                }
                // only do a join if column name is specified
                //else if (isset($request['search'])) {
                //    $joinSearch = $request['search'];
                //}

                if (!empty($joinSearch)) {
                    $ti = array_search($associated[$f]['targetEntity'], self::$allTableClasses);
                    $joinName = self::$allTableMetadata[$ti]->table['name'];
                    if (!in_array($joinName, $joins)) {
                        $joins[] = $joinName;
                        $qb = $qb->leftJoin($table . '.' . $f, $joinName);
                    }
                    $qb = self::newSearchBuilder($qb, $joinName, ['search' => $joinSearch], $joins);
                }
            }
        }

        return $qb;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        set_time_limit(0);
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var $user User */
        $user = $this->getUser();
        if(!$user->hasRole('ROLE_ADMIN')) {
            throw new AccessDeniedHttpException();
        }

        self::$tables = [
            'ss_user' => ['id', 'name', 'groups', 'packs', 'roles', 'actions'],
            'ss_group' => ['id', 'name', 'users', 'packs', 'roles', 'actions'],
            'pack' => ['id', 'name', 'groups', 'users', 'status', 'actions'],
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

        $vars = ['tables' => self::$tables];

        foreach(self::$tables as $table => $t) {
            /** @var QueryBuilder $qb */
            $qb = $orm->getRepository(self::$allTables[$table]->name)->createQueryBuilder($table);
            $qb = self::newSearchBuilder($qb, $table, $request->query->all());
            $total = $qb->select('COUNT(DISTINCT ' . $table . '.id)')
                ->getQuery()
                ->getSingleScalarResult();
            $vars[$table . '_total'] = $total;

            // max pagination to search count
            if(!empty($page = $request->get('page'))) {
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

            $vars[$table] = self::newSearchBuilder($orm->getRepository(self::$allTables[$table]->name)->createQueryBuilder($table), $table, $request->query->all())
                ->setFirstResult($resultOffset)
                ->setMaxResults(25)
                ->getQuery()
                ->getResult();
        }

        return $this->render('AdminBundle:Admin:tab.html.php', $vars);
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

        return $this->indexAction($request);
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

        return $this->forward('AdminBundle:Admin:index', ['_format' => 'tab']);
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
