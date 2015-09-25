<?php
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Partner;
use StudySauce\Bundle\Entity\PartnerInvite;
use StudySauce\Bundle\Entity\Schedule;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\Entity\Visit;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

/** @var User $user */
$user = $app->getUser();
/** @var $partner PartnerInvite */
$permissions = !empty($partner) ? $partner->getPermissions() : [
    'goals',
    'metrics',
    'deadlines',
    'uploads',
    'plan',
    'profile'
];

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(['@AdminBundle/Resources/public/css/userlist.css'],[],['output' => 'bundles/admin/css/*.css']) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(['@AdminBundle/Resources/public/js/userlist.js'],[],['output' => 'bundles/admin/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('body'); ?>
<div class="panel-pane" id="userlist">
    <div class="pane-content">
        <form action="<?php print $view['router']->generate('save_group'); ?>" method="post">
            <div class="search">
                <label class="input"><input name="search" type="text" value="" placeholder="Search"/></label>
            </div>
        <div id="select-status" style="display: none;">
            <a href="#green"><span>&nbsp;</span></a>
            <a href="#yellow"><span>&nbsp;</span></a>
            <a href="#red"><span>&nbsp;</span></a></div>
        <table class="<?php print ($user->hasRole('ROLE_MASTER_ADVISER') && $user->getGroups()->count() > 1 ? 'master' : ''); ?>">
            <thead>
            <tr>
                <th><select>
                        <option>Status</option>
                        <option>Ascending</option>
                        <option>Descending</option>
                        <option>Red</option>
                        <option>Yellow</option>
                        <option>Green</option>
                    </select></th>
                <th><select>
                        <option>Date</option>
                        <option>Ascending (A-Z)</option>
                        <option>Descending (Z-A)</option>
                    </select></th>
                <th><select>
                        <option>Student</option>
                        <option>Ascending (A-Z)</option>
                        <option>Descending (Z-A)</option>
                    </select></th>
                <th><select>
                        <option>Completion</option>
                        <option>Ascending (A-Z)</option>
                        <option>Descending (Z-A)</option>
                    </select></th>
                <th><select>
                        <option>School</option>
                        <option>Ascending (A-Z)</option>
                        <option>Descending (Z-A)</option>
                    </select></th>
                <?php if($user->hasRole('ROLE_MASTER_ADVISER') && $user->getGroups()->count() > 1) { ?>
                    <th><select>
                            <option>Adviser</option>
                            <option>Ascending (A-Z)</option>
                            <option>Descending (Z-A)</option>
                        </select></th>
                <?php } ?>
                <th><select name="hasDeadlines">
                        <option>Deadlines</option>
                        <option>Y</option>
                        <option>N</option>
                    </select></th>
                <th><select name="hasSchedule">
                        <option>Schedule</option>
                        <option>Y</option>
                        <option>N</option>
                    </select></th>
                <th><select name="hasGrades">
                            <option>Grades</option>
                            <option>Y</option>
                            <option>N</option>
                        </select></th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach($users as $i => $u)
            {
                /** @var User $u */
                if($u->hasRole('ROLE_ADVISER') || $u->hasRole('ROLE_MASTER_ADVISER') || $u->hasRole('ROLE_ADMIN'))
                    continue;
                /** @var Visit $v */
                $v = $u->getVisits()->first();
                $parts = explode('/', !empty($v) ? $v->getPath() : '/');
                $path = implode('', $parts);
                $uri = $view['router']->generate('adviser', ['_user' => $u->getId(), '_tab' => $path == '' ? 'home' : $path]);
                /** @var Schedule $schedule */
                $schedule = $u->getSchedules()->first();
                /** @var User $adviser */
                $adviser = $u->getPartnerOrAdviser();
                $ts = !empty($u->getLastVisit()) ? $u->getLastVisit() : $u->getCreated();
                ?><tr class="user-id-<?php print $u->getId(); ?> status_<?php print ($u->getProperty('adviser_status') ?: 'green'); ?>">
                <td><a href="#change-status"><span>&nbsp;</span></a></td>
                <td data-timestamp="<?php print $ts->getTimestamp(); ?>"><?php print $ts->format('j M'); ?></td>
                <td><a title="<?php print (!empty($parts[1]) ? ucfirst($parts[1]) : 'Home'); ?>" href="<?php print $uri; ?>">
                        <?php print $u->getFirst() . ' ' . $u->getLast(); ?></a></td>
                <td><?php print $u->getCompleted(); ?>%</td>
                <td><?php print (empty($schedule) || empty($schedule->getUniversity()) ? 'Not set' : $schedule->getUniversity()); ?></td>
                <?php if($user->hasRole('ROLE_MASTER_ADVISER') && $user->getGroups()->count() > 1) { ?>
                    <td><?php print (!empty($u->getGroups()->first()) ? $u->getGroups()->first()->getName() : (!empty($adviser) ? ($adviser->getFirst() . ' ' . $adviser->getLast()) : 'Not assigned')); ?></td>
                <?php } ?>
                <td data-value="<?php print ($u->getDeadlines()->count() > 0 ? 'Y' : 'N'); ?>"><?php print $u->getDeadlines()->count(); ?></td>
                <td data-value="<?php print $u->getSchedules()->count(); ?>"><?php print ($u->getSchedules()->count() > 0 ? 'Y' : 'N'); ?></td>
                <td data-value="<?php
                $gradesCount = array_sum($u->getSchedules()->map(function (Schedule $s) {
                    return $s->getCourses()->filter(function (Course $c) {
                        return $c->getGrades()->count() > 0;
                    })->count();
                })->toArray());
                print ($gradesCount > 0 ? 'Y' : 'N'); ?>"><?php print $gradesCount; ?></td>
                </tr><?php
            } ?>
            </tbody>
        </table>
        </form>
    </div>
</div>
<?php $view['slots']->stop();

$view['slots']->start('sincludes');
if($showPartnerIntro) {
    echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'partner-advice-1']));
    echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'partner-advice-2']), ['strategy' => 'sinclude']);
    echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'partner-advice-3']), ['strategy' => 'sinclude']);
    echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'partner-advice-4']), ['strategy' => 'sinclude']);
}
$view['slots']->stop();
