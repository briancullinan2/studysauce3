<?php


namespace Admin\Bundle\Controller;


use Course1\Bundle\Entity\Course1;
use Course2\Bundle\Entity\Course2;
use Course3\Bundle\Entity\Course3;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use StudySauce\Bundle\Controller\GoalsController;
use StudySauce\Bundle\Controller\MetricsController;
use StudySauce\Bundle\Controller\PlanController;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\PartnerInvite;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\Entity\Visit;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class AdviserController
 */
class AdviserController extends Controller
{

    /**
     * @return Response
     */
    public function userlistAction()
    {
        set_time_limit(0);
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var $user User */
        $user = $this->getUser();
        if(!$user->hasRole('ROLE_ADVISER') && !$user->hasRole('ROLE_MASTER_ADVISER') && !$user->hasRole('ROLE_PARTNER')) {
            throw new AccessDeniedHttpException();
        }
        elseif($user->hasRole('ROLE_ADVISER') || $user->hasRole('ROLE_MASTER_ADVISER')) {
            $users = [];
            foreach ($user->getGroups()->toArray() as $i => $g) {
                /** @var Group $g */
                $users = array_merge($users, $g->getUsers()->toArray());
            }
        }
        else {
            $users = [];
        }

        /** @var PartnerInvite $partner */
        $partner = $orm->getRepository('StudySauceBundle:PartnerInvite')->findBy(['partner' => $user->getId()]);
        foreach($partner as $j => $p)
        {
            /** @var PartnerInvite $p */
            $users[] = $p->getUser();
        }

        $users = array_unique($users);

        $showPartnerIntro = false;
        if(count($users) && empty($user->getProperty('seen_partner_intro'))) {
            $showPartnerIntro = true;
            /** @var $userManager UserManager */
            $userManager = $this->get('fos_user.user_manager');
            $user->setProperty('seen_partner_intro', true);
            $userManager->updateUser($user);
        }

        usort($users, function (User $b, User $a) {
            return (!empty($a->getLastVisit()) ? $a->getLastVisit()->getTimestamp() : 0) - (!empty($b->getLastVisit()) ? $b->getLastVisit()->getTimestamp() : 0);
        });

        return $this->render('AdminBundle:Adviser:userlist.html.php', [
            'total' => count($users),
            'users' => $users,
            'showPartnerIntro' => $showPartnerIntro
        ]);
    }

    /**
     * @param $_user
     * @param $_tab
     * @return Response
     */
    public function adviserAction(User $_user, $_tab)
    {
        $u = $this->getUser();

        if(!$u->hasRole('ROLE_PARTNER') && !$u->hasRole('ROLE_ADVISER') && !$u->hasRole('ROLE_MASTER_ADVISER') &&
            !$u->hasRole('ROLE_ADMIN'))
            throw new AccessDeniedHttpException();

        return $this->render('AdminBundle:Adviser:adviser.html.php', [
            'user' => $_user,
            'tab' => $_tab
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateStatusAction(Request $request)
    {
        /** @var $userManager UserManager */
        $userManager = $this->get('fos_user.user_manager');

        /** @var $user User */
        $user = $userManager->findUserBy(['id' => intval($request->get('userId'))]);

        // TODO: check if partner and user is connected
        $user->setProperty('adviser_status', $request->get('status'));
        $userManager->updateUser($user);
        return new JsonResponse(true);
    }

    /**
     * @param User $_user
     * @return Response
     */
    public function goalsAction(User $_user)
    {
        $goals = new GoalsController();
        $goals->setContainer($this->container);
        return $goals->indexAction($_user, ['Partner', 'goals']);
    }

    /**
     * @param User $_user
     * @return Response
     */
    public function metricsAction(User $_user)
    {
        $metrics = new MetricsController();
        $metrics->setContainer($this->container);
        return $metrics->indexAction($_user, ['Partner', 'metrics']);
    }

    /**
     * @param Request $request
     * @param User $_user
     * @return Response
     */
    public function deadlinesAction(Request $request, User $_user)
    {
        $deadlines = new \StudySauce\Bundle\Controller\DeadlinesController();
        $deadlines->setContainer($this->container);
        return $deadlines->indexAction($request, $_user, ['Partner', 'deadlines']);
    }

    /**
     * @param User $_user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function planAction(User $_user = null)
    {
        $plan = new PlanController();
        $plan->setContainer($this->container);
        return $plan->indexAction($_user, ['Partner', 'plan']);
    }

    /**
     * @param Request $request
     * @param User $_user
     * @return Response
     */
    public function resultsAction(Request $request, User $_user)
    {
        return $this->render('AdminBundle:Adviser:results.html.php', [
            'course1' => $_user->getCourse1s()->first() ?: new Course1(),
            'course2' => $_user->getCourse2s()->first() ?: new Course2(),
            'course3' => $_user->getCourse3s()->first() ?: new Course3()
        ]);
    }
}