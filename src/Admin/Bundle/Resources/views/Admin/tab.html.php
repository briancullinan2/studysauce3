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

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(['@results_css'], [], ['output' => 'bundles/admin/css/*.css']) as $url) { ?>
    <link type="text/css" rel="stylesheet" href="<?php print ($view->escape($url)); ?>"/>
<?php }
foreach ($view['assetic']->stylesheets(['@AdminBundle/Resources/public/css/admin.css'], [], ['output' => 'bundles/admin/css/*.css']) as $url) { ?>
    <link type="text/css" rel="stylesheet" href="<?php print ($view->escape($url)); ?>"/>
<?php }
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(['@AdminBundle/Resources/public/js/results.js'], [], ['output' => 'bundles/admin/js/*.js']) as $url) { ?>
    <script type="text/javascript" src="<?php print ($view->escape($url)); ?>"></script>
<?php }
foreach ($view['assetic']->javascripts(['@AdminBundle/Resources/public/js/admin.js'], [], ['output' => 'bundles/admin/js/*.js']) as $url) { ?>
    <script type="text/javascript" src="<?php print ($view->escape($url)); ?>"></script>
<?php } ?>
<?php $view['slots']->stop();

$view['slots']->start('body'); ?>
    <div class="panel-pane" id="command">
        <div class="pane-content">
            <form action="<?php print ($view['router']->generate('save_user')); ?>" class="user-edit">
            <?php
            $request = (array)(new stdClass());
            $request['count-ss_user'] = 50;
            $request['count-ss_group'] = -1;
            $request['tables'] = (array)(new stdClass());
            $request['tables']['ss_user'] = ['id' => ['lastVisit', 'id'], 'title' => ['first', 'last', 'email'], 'membership' => ['groups', 'roles', 'properties'], '10' => 'actions'];
            $request['tables']['ss_group'] = ['id', 'name'];
            $request['classes'] = [];
            $request['headers'] = false;
            $request['footers'] = false;
            print ($view['actions']->render(new ControllerReference('AdminBundle:Admin:results', $request))); ?>
            </form>
        </div>
    </div>
<?php $view['slots']->stop();

$view['slots']->start('sincludes');
$view['slots']->stop();
