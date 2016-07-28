<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\ORM\EntityManager;
use StudySauce\Bundle\Command\CronSauceCommand;
use StudySauce\Bundle\Entity\Invite;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class LandingController
 * @package StudySauce\Bundle\Controller
 */
class LandingController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        /** @var User $user */
        $user = $this->getUser();
        $session = $request->getSession();

        // check if we have a user and redirect accordingly.
        list($route, $options) = HomeController::getUserRedirect($user, $this->container);
        if($route != '_welcome')
            return $this->redirect($this->generateUrl($route, $options));

        /** @var Invite $group */
        // TODO: generalize this for other groups
        $group = $orm->getRepository('StudySauceBundle:Invite')->findOneBy(['code' => $request->get('_code')]);
        if(!empty($group) && $group->getGroup()->getName() == 'Torch And Laurel' ||
            ($session->has('organization') && $session->get('organization') == 'Torch And Laurel'))
        {
            return $this->forward('TorchAndLaurelBundle:Landing:index');
        }

        return $this->render('StudySauceBundle:Landing:index2.html.php');
    }

    /**
     * @param null $options
     * @return JsonResponse
     * @throws \Exception
     */
    public function cronAction($options = null)
    {
        if(is_string($options))
            $options = explode(',', $options);
        $command = new CronSauceCommand();
        $command->setContainer($this->container);
        $input = new ArrayInput(!empty($options)
            ? array_combine(
                array_map(function ($k) {return '--' . $k;}, $options),
                array_map(function () {return true;}, $options))
            : [] /* array('some-param' => 10, '--some-option' => true)*/);
        $output = new NullOutput();
        $command->run($input, $output);
        return new JsonResponse(true);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function termsAction()
    {
        return $this->render('StudySauceBundle:Landing:terms.html.php');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function privacyAction()
    {
        return $this->render('StudySauceBundle:Landing:privacy.html.php');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function aboutAction()
    {
        return $this->render('StudySauceBundle:Landing:about.html.php');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function refundAction()
    {
        return $this->render('StudySauceBundle:Landing:refund.html.php');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function contactAction()
    {
        return $this->render('StudySauceBundle:Landing:contact.html.php');
    }

    /**
     * Do nothing
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function visitAction(Request $request)
    {
        // call visits for other bundles
        //$course = new

        // TODO: recording logic
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        $visits = $request->get('__visits');
        $visits[]['path'] = $request->getPathInfo();
        foreach ($visits as $i => $v) {
            if(!empty($base = $request->getBaseUrl()) && strpos($v['path'], $base) == 0)
                    $v['path'] = substr($v['path'], strlen($request->getBaseUrl()));
            if(substr($v['path'], 0, 1) != '/')
                $v['path'] = '/' . $v['path'];
            if(substr(str_replace($request->getBaseUrl(), '', $v['path']), 0, 10) == '/course/1/') {
                // TODO: check for quiz completeness
                if (preg_match('/lesson\/([0-9]+)\/step\/?([0-9]+)?/', $v['path'], $matches)) {
                    // compare course progress

                    /** @var $user User */
                    $user = $this->getUser();

                    /** @var Course1 $course */
                    $course = $user->getCourse1s()->first();

                    if (!empty($course)) {
                        $lesson = intval($matches[1]);
                        $step = isset($matches[2]) ? intval($matches[2]) : 0;
                        if ($lesson === 1 && $course->getLesson1() < $step) {
                            $course->setLesson1($step);
                        }
                        if ($lesson === 2 && $course->getLesson2() < $step) {
                            $course->setLesson2($step);
                        }
                        if ($lesson === 3 && $course->getLesson3() < $step) {
                            $course->setLesson3($step);
                        }
                        if ($lesson === 4 && $course->getLesson4() < $step) {
                            $course->setLesson4($step);
                        }
                        if ($lesson === 5 && $course->getLesson5() < $step) {
                            $course->setLesson5($step);
                        }
                        if ($lesson === 6 && $course->getLesson6() < $step) {
                            $course->setLesson6($step);
                        }
                        if ($lesson === 7 && $course->getLesson7() < $step) {
                            $course->setLesson7($step);
                        }
                        $orm->merge($course);
                        $orm->flush();
                    }
                }
            }
            elseif(substr(str_replace($request->getBaseUrl(), '', $v['path']), 0, 10) == '/course/2/') {
                // TODO: check for quiz completeness
                if (preg_match('/lesson\/([0-9]+)\/step\/?([0-9]+)?/', $v['path'], $matches)) {
                    // compare course progress

                    /** @var $user User */
                    $user = $this->getUser();

                    /** @var Course2 $course */
                    $course = $user->getCourse2s()->first();

                    if (!empty($course)) {
                        $lesson = intval($matches[1]);
                        $step = isset($matches[2]) ? intval($matches[2]) : 0;
                        if ($lesson === 1 && $course->getLesson1() < $step) {
                            $course->setLesson1($step);
                        }
                        if ($lesson === 2 && $course->getLesson2() < $step) {
                            $course->setLesson2($step);
                        }
                        if ($lesson === 3 && $course->getLesson3() < $step) {
                            $course->setLesson3($step);
                        }
                        if ($lesson === 4 && $course->getLesson4() < $step) {
                            $course->setLesson4($step);
                        }
                        if ($lesson === 5 && $course->getLesson5() < $step) {
                            $course->setLesson5($step);
                        }
                        $orm->merge($course);
                        $orm->flush();
                    }
                }
            }
            elseif(substr(str_replace($request->getBaseUrl(), '', $v['path']), 0, 10) == '/course/3/') {
                // TODO: check for quiz completeness
                if (preg_match('/lesson\/([0-9]+)\/step\/?([0-9]+)?/', $v['path'], $matches)) {
                    // compare course progress

                    /** @var $user User */
                    $user = $this->getUser();

                    /** @var Course3 $course */
                    $course = $user->getCourse3s()->first();

                    if (!empty($course)) {
                        $lesson = intval($matches[1]);
                        $step = isset($matches[2]) ? intval($matches[2]) : 0;
                        if ($lesson === 1 && $course->getLesson1() < $step) {
                            $course->setLesson1($step);
                        }
                        if ($lesson === 2 && $course->getLesson2() < $step) {
                            $course->setLesson2($step);
                        }
                        if ($lesson === 3 && $course->getLesson3() < $step) {
                            $course->setLesson3($step);
                        }
                        if ($lesson === 4 && $course->getLesson4() < $step) {
                            $course->setLesson4($step);
                        }
                        if ($lesson === 5 && $course->getLesson5() < $step) {
                            $course->setLesson5($step);
                        }
                        $orm->merge($course);
                        $orm->flush();
                    }
                }
            }
        }

        return new JsonResponse(true);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function partnersAction(Request $request)
    {
        $session = $request->getSession();

        if(empty($session->get('partner')))
            $session->set('partner', true);

        return $this->render('StudySauceBundle:Landing:partners.html.php');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function parentsAction(Request $request)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        $session = $request->getSession();

        if(empty($session->get('parent')))
            $session->set('parent', true);

        /** @var Invite $group */
        // TODO: generalize this for other groups
        $group = $orm->getRepository('StudySauceBundle:Invite')->findOneBy(['code' => $request->get('_code')]);
        if(!empty($group) && $group->getGroup()->getName() == 'Torch And Laurel' ||
            ($session->has('organization') && $session->get('organization') == 'Torch And Laurel'))
        {
            return $this->forward('TorchAndLaurelBundle:Landing:parents');
        }

        return $this->render('StudySauceBundle:Landing:parents.html.php');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function studentsAction(Request $request)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        $session = $request->getSession();

        /** @var Invite $group */
        // TODO: generalize this for other groups
        $group = $orm->getRepository('StudySauceBundle:Invite')->findOneBy(['code' => $request->get('_code')]);
        if(!empty($group) && $group->getGroup()->getName() == 'Torch And Laurel' ||
            ($session->has('organization') && $session->get('organization') == 'Torch And Laurel'))
        {
            return $this->forward('TorchAndLaurelBundle:Landing:index');
        }

        return $this->render('StudySauceBundle:Landing:students.html.php');
    }

}
