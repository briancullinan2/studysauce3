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


        // check for individual lesson filters
        for ($i = 1; $i <= 17; $i++) {
            if (!empty($lesson = $request->get('lesson' . $i))) {
                if (!in_array('c1', $joins)) {
                    $qb = $qb
                        ->leftJoin('u.course1s', 'c1')
                        ->leftJoin('u.course2s', 'c2')
                        ->leftJoin('u.course3s', 'c3');
                    $joins[] = 'c1';
                }
                if ($i > 12) {
                    $l = $i - 12;
                    $c = 3;
                } elseif ($i > 7) {
                    $l = $i - 7;
                    $c = 2;
                } else {
                    $l = $i;
                    $c = 1;
                }
                if ($lesson == 'yes') {
                    $qb = $qb->andWhere('c' . $c . '.lesson' . $l . '=4');
                } else {
                    $qb = $qb->andWhere('c' . $c . '.lesson' . $l . '<4 OR ' . 'c' . $c . '.lesson' . $l . ' IS NULL');
                }
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
        set_time_limit(0);
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var $user User */
        $user = $this->getUser();
        if (!$user->hasRole('ROLE_ADMIN')) {
            throw new AccessDeniedHttpException();
        }

        // count total so we know the max pages
        $total = self::searchBuilder($orm, $request)
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
        $users = self::searchBuilder($orm, $request, $joins)->distinct(true)->select('u');

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
            if ($field == 'completed') {
                if (!in_array('c1', $joins)) {
                    $users = $users
                        ->leftJoin('u.course1s', 'c1')
                        ->leftJoin('u.course2s', 'c2')
                        ->leftJoin('u.course3s', 'c3');
                }
                $users = $users
                    ->addOrderBy('c1.lesson1 + c1.lesson2 + c1.lesson3 + c1.lesson4 + c1.lesson5 + c1.lesson6 + c1.lesson7 + c2.lesson1 + c2.lesson2 + c2.lesson3 + c2.lesson4 + c2.lesson5 + c3.lesson1 + c3.lesson2 + c3.lesson3 + c3.lesson4 + c3.lesson5', $direction)
                    ->addOrderBy('c1.lesson1 + c1.lesson2 + c1.lesson3 + c1.lesson4 + c1.lesson5 + c1.lesson6 + c1.lesson7', $direction)
                    ->addOrderBy('c2.lesson1 + c2.lesson2 + c2.lesson3 + c2.lesson4 + c2.lesson5', $direction)
                    ->addOrderBy('c3.lesson1 + c3.lesson2 + c3.lesson3 + c3.lesson4 + c3.lesson5', $direction);
                $joins[] = 'c1';
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
        $courses = [
            'course1' => [
                'Course1Bundle:Introduction:quiz.html.php' => 'quiz1',
                'Course1Bundle:SettingGoals:quiz.html.php' => 'quiz2',
                'Course1Bundle:Distractions:quiz.html.php' => 'quiz4',
                'Course1Bundle:Procrastination:quiz.html.php' => 'quiz3',
                'Course1Bundle:Environment:quiz.html.php' => 'quiz5',
                'Course1Bundle:Partners:quiz.html.php' => 'quiz6'
            ],
            'course2' => [
                'Course2Bundle:StudyMetrics:quiz.html.php' => 'studyMetrics',
                'Course2Bundle:StudyPlan:quiz.html.php' => 'studyPlan',
                'Course2Bundle:Interleaving:quiz.html.php' => 'interleaving',
                'Course2Bundle:StudyTests:quiz.html.php' => 'studyTests',
                'Course2Bundle:TestTaking:quiz.html.php' => 'testTaking',
            ],
            'course3' => [
                'Course3Bundle:Strategies:quiz.html.php' => 'strategies',
                'Course3Bundle:GroupStudy:quiz.html.php' => 'groupStudy',
                'Course3Bundle:Teaching:quiz.html.php' => 'teaching',
                'Course3Bundle:ActiveReading:quiz.html.php' => 'activeReading',
                'Course3Bundle:SpacedRepetition:quiz.html.php' => 'spacedRepetition',
            ],
        ];
        foreach ($courses as $course => $quizes) {
            foreach ($quizes as $t => $q) {
                $data = $orm->getMetadataFactory()->getMetadataFor(ucfirst($course) . 'Bundle:' . ucfirst($q));
                $fields = $data->getFieldNames();
                $questions = [];
                foreach ($fields as $f) {
                    if (in_array($f, $data->getAssociationNames()) || $f == 'id' || $f == 'created') {
                        continue;
                    }

                    $groupBy = self::searchBuilder($orm, $request, $joins)->distinct(true)->select(
                        'q1.' . $f . ', count(q1) AS cnt'
                    );
                    if (!in_array('c1', $joins)) {
                        $groupBy = $groupBy->leftJoin('u.' . $course . 's', 'c' . substr($course, -1));
                        $joins[] = 'c1';
                    }
                    $counts = $groupBy
                        ->leftJoin('c' . substr($course, -1) . '.' . $q, 'q1')
                        ->groupBy('q1.' . $f)
                        ->getQuery()
                        ->getResult();
                    $questions[$f] = $counts;
                }
                $cn = '\\' . ucfirst($course) . '\\Bundle\\Entity\\' . ucfirst($q);
                $quizContent = $this->forward('AdminBundle:Results:template', ['_format' => 'tab', 'exclude_layout' => true, 'template' => $t, 'class' => $cn])->getContent();
                foreach ($questions as $f => $c) {
                    $total = array_sum(
                        array_map(
                            function ($field) {
                                return $field['cnt'];
                            },
                            $c
                        )
                    );
                    $answers = '<div class="response">' . join(
                            '</div><div class="response">',
                            array_map(
                                function ($field) use ($f, $total) {
                                    $val = $field[$f];
                                    if (is_array($val)) {
                                        $val = join(', ', $val);
                                    }
                                    if (is_bool($val)) {
                                        $val = $val ? 'true' : 'false';
                                    }
                                    if (is_null($val)) {
                                        $val = 'No answer';
                                    }

                                    return $val . ' - ' . $field['cnt'] . ' (' . ($total == 0 ? 0 : round(
                                        $field['cnt'] * 100.0 / $total
                                    )) . '%)';
                                },
                                $c
                            )
                        ) . '</div>';
                    $quizContent = preg_replace(
                        '/(<label[\s\S]*?<(input|textarea).*name="quiz-' . $f . '".*?>[\s\S]*?<\/label>[\s]*)+/i',
                        $answers,
                        $quizContent
                    );
                }
                $aggregate[$q] = $quizContent;
            }
        }

        // get why study? answers
        /** @var QueryBuilder $whyStudy */
        $whyStudy = self::searchBuilder($orm, $request, $joins)->distinct(true)->select(
            'c1.whyStudy, count(c1) AS cnt'
        );
        if (!in_array('c1', $joins)) {
            $whyStudy = $whyStudy->leftJoin('u.course1s', 'c1');
            $joins[] = 'c1';
        }
        $whyStudy = $whyStudy->groupBy('c1.whyStudy')
            ->getQuery()
            ->getResult();
        $aggregate['whyStudy'] = '<div class="response">' . join(
                '</div><div class="response">',
                array_map(
                    function ($field) use ($total) {
                        $val = $field['whyStudy'];
                        if (is_array($val)) {
                            $val = join(', ', $val);
                        }
                        if (is_bool($val)) {
                            $val = $val ? 'true' : 'false';
                        }
                        if (is_null($val)) {
                            $val = 'No answer';
                        }

                        return $val . ' - ' . $field['cnt'];
                    },
                    $whyStudy
                )
            ) . '</div>';

        // get all feedback
        /** @var QueryBuilder $feedback */
        $feedback = self::searchBuilder($orm, $request, $joins)->distinct(true)->select(
            'c3.feedback, count(c3) AS cnt'
        );
        if (!in_array('c1', $joins)) {
            $feedback = $feedback->leftJoin('u.course3s', 'c3');
            $joins[] = 'c1';
        }
        $feedback = $feedback->groupBy('c3.feedback')
            ->getQuery()
            ->getResult();
        $aggregate['feedback'] = '<div class="response">' . join(
                '</div><div class="response">',
                array_map(
                    function ($field) use ($total) {
                        $val = $field['feedback'];
                        if (is_array($val)) {
                            $val = join(', ', $val);
                        }
                        if (is_bool($val)) {
                            $val = $val ? 'true' : 'false';
                        }
                        if (is_null($val)) {
                            $val = 'No answer';
                        }

                        return $val . ' - ' . $field['cnt'];
                    },
                    $feedback
                )
            ) . '</div>';

        // get net promoter score
        $good = 0.0;
        $bad = 0.0;
        $netTotal = 0.0;
        /** @var QueryBuilder $netPromoter */
        $netPromoter = self::searchBuilder($orm, $request, $joins)->distinct(true)->select(
            'c3.netPromoter, count(c3) AS cnt'
        );
        if (!in_array('c1', $joins)) {
            $netPromoter = $netPromoter->leftJoin('u.course3s', 'c3');
            $joins[] = 'c1';
        }
        $netPromoter = $netPromoter->groupBy('c3.netPromoter')
            ->getQuery()
            ->getResult();
        $aggregate['net-promoter'] = '<div class="response">' . join(
                '</div><div class="response">',
                array_map(
                    function ($field) use ($total, &$good, &$bad, &$netTotal) {
                        $val = $field['netPromoter'];
                        if (is_array($val)) {
                            $val = join(', ', $val);
                        }
                        if (is_bool($val)) {
                            $val = $val ? 'true' : 'false';
                        }
                        if (is_null($val)) {
                            $val = 'No answer';
                        }
                        if (is_numeric($val)) {
                            $val = empty($val) ? 'No answer' : $val;
                        }
                        if ($val >= 9) {
                            $good += $field['cnt'];
                        }
                        if ($val <= 6 && $val > 0) {
                            $bad += $field['cnt'];
                        }
                        if ($val > 0) {
                            $netTotal += $field['cnt'];
                        }

                        return $val . ' - ' . $field['cnt'] . ' (' . round(
                            $field['cnt'] * 100.0 / $total
                        ) . '%)';
                    },
                    $netPromoter
                )
            ) . '</div>';
        $aggregate['net-promoter'] .= '<div class="response">Total: ' . round(($good / $netTotal - $bad / $netTotal) * 100) . '</div>';

        $parents = self::searchBuilder($orm, $request)
            ->select('COUNT(DISTINCT u.id)')
            ->andWhere('u.roles LIKE \'%s:11:"ROLE_PARENT"%\'')
            ->getQuery()
            ->getSingleScalarResult();

        $partners = self::searchBuilder($orm, $request)
            ->select('COUNT(DISTINCT u.id)')
            ->andWhere('u.roles LIKE \'%s:12:"ROLE_PARTNER"%\'')
            ->getQuery()
            ->getSingleScalarResult();

        $advisers = self::searchBuilder($orm, $request)
            ->select('COUNT(DISTINCT u.id)')
            ->andWhere('u.roles LIKE \'%s:12:"ROLE_ADVISER"%\' OR u.roles LIKE \'%s:19:"ROLE_MASTER_ADVISER"%\'')
            ->getQuery()
            ->getSingleScalarResult();

        $students = self::searchBuilder($orm, $request)
            ->select('COUNT(DISTINCT u.id)')
            ->andWhere('u.roles NOT LIKE \'%s:12:"ROLE_ADVISER"%\'')
            ->andWhere('u.roles NOT LIKE \'%s:19:"ROLE_MASTER_ADVISER"%\'')
            ->andWhere('u.roles NOT LIKE \'%s:12:"ROLE_PARTNER"%\'')
            ->andWhere('u.roles NOT LIKE \'%s:11:"ROLE_PARENT"%\'')
            ->getQuery()
            ->getSingleScalarResult();

        /** @var QueryBuilder $torch */
        $torch = self::searchBuilder($orm, $request, $joins);
        if (!in_array('g', $joins)) {
            $torch = $torch->leftJoin('u.groups', 'g');
        }
        $torch = $torch->select('COUNT(DISTINCT u.id)')
            ->andWhere('g.name LIKE \'%torch%\'')
            ->getQuery()
            ->getSingleScalarResult();
        /** @var int $torch */

        /** @var QueryBuilder $csa */
        $csa = self::searchBuilder($orm, $request, $joins);
        if (!in_array('g', $joins)) {
            $csa = $csa->leftJoin('u.groups', 'g');
        }
        $csa = $csa->select('COUNT(DISTINCT u.id)')
            ->andWhere('g.name LIKE \'%csa%\'')
            ->getQuery()
            ->getSingleScalarResult();
        /** @var int $csa */

        /** @var QueryBuilder $paid */
        $paid = self::searchBuilder($orm, $request, $joins);
        if (!in_array('g', $joins)) {
            $paid = $paid->leftJoin('u.groups', 'g');
        }
        $paid = $paid->select('COUNT(DISTINCT u.id)')
            ->andWhere('u.roles LIKE \'%s:9:"ROLE_PAID"%\' OR g.id IN (' . self::$paidStr . ')')
            ->getQuery()
            ->getSingleScalarResult();
        /** @var int $paid */

        /** @var QueryBuilder $completed */
        $completed = self::searchBuilder($orm, $request, $joins);
        if (!in_array('c1', $joins)) {
            $completed = $completed
                ->leftJoin('u.course1s', 'c1')
                ->leftJoin('u.course2s', 'c2')
                ->leftJoin('u.course3s', 'c3');
        }
        $completed = $completed->select('COUNT(DISTINCT u.id)')
            ->andWhere('c1.lesson1=4 AND c1.lesson2=4 AND c1.lesson3=4 AND c1.lesson4=4 AND c1.lesson5=4 AND c1.lesson6=4')
            ->andWhere('c2.lesson1=4 AND c2.lesson2=4 AND c2.lesson3=4 AND c2.lesson4=4 AND c2.lesson5=4')
            ->andWhere('c3.lesson1=4 AND c3.lesson2=4 AND c3.lesson3=4 AND c3.lesson4=4 AND c3.lesson5=4')
            ->getQuery()
            ->getSingleScalarResult();


        // get the groups for use in dropdown
        $groups = $orm->getRepository('StudySauceBundle:Group')->findAll();

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
            'completed' => $completed,
            'total' => $total,
            'aggregate' => $aggregate
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