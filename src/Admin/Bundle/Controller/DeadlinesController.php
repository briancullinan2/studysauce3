<?php

namespace Admin\Bundle\Controller;


use StudySauce\Bundle\Entity\Deadline;
use StudySauce\Bundle\Entity\Schedule;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DeadlinesController
 */
class DeadlinesController extends Controller
{
    /**
     * @param Request $request
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, User $user = null)
    {
        /** @var $user \StudySauce\Bundle\Entity\User */
        if(empty($user))
            $user = $this->getUser();
        $deadlines = $user->getDeadlines()->filter(function (Deadline $d) {return !$d->getDeleted();});

        /** @var Schedule $schedule */
        $schedule = $user->getSchedules()->first();
        if(!empty($schedule))
            $courses = $schedule->getClasses()->toArray();
        else
            $courses = [];

        // show new deadline and hide headings if all the deadlines are in the past
        $isEmpty = false;
        if(!$deadlines->exists(function ($_, Deadline $d) { return $d->getDueDate() >= date_sub(new \Datetime('today'), new \DateInterval('P1D')); })) {
            $isEmpty = true;
        }

        $csrfToken = $this->has('form.csrf_provider')
            ? $this->get('form.csrf_provider')->generateCsrfToken('update_deadlines')
            : null;

        return $this->render('AdminBundle:Deadlines:tab.html.php', [
            'csrf_token' => $csrfToken,
            'deadlines' => $deadlines->toArray(),
            'courses' => array_values($courses),
            'user' => $user,
            'isEmpty' => $isEmpty
        ]);
    }

}