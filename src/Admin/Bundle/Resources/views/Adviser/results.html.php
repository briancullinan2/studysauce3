<?php
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Partner;
use StudySauce\Bundle\Entity\User;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

/** @var User $user */
$user = $app->getUser();

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('results-body'); ?>
<div class="panel-pane" id="results">
    <div class="pane-content">
        <?php echo $view->render('StudySauceBundle:Partner:partner-nav.html.php', ['user' => $user]); ?>
        <h2>Quiz Results</h2>
        <table class="results">
            <tbody>
            <tr>
                <td>
                    <?php print $view->render('AdminBundle:Results:result.html.php', [
                        'course1' => $course1,
                        'course2' => $course2,
                        'course3' => $course3
                    ]); ?>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<?php $view['slots']->stop();

$view['slots']->start('body');
$view['slots']->output('results-body');
$view['slots']->stop();

$view['slots']->start('stylesheets'); ?>
<?php foreach ($view['assetic']->stylesheets(['@AdminBundle/Resources/public/css/menu.css'],[],['output' => 'bundles/admin/css/*.css']) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
foreach ($view['assetic']->stylesheets(['@AdminBundle/Resources/public/css/results.css'],[],['output' => 'bundles/admin/css/*.css']) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts'); ?>
<?php foreach ($view['assetic']->javascripts(['@AdminBundle/Resources/public/js/results.js'],[],['output' => 'bundles/admin/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach; ?>
<?php $view['slots']->stop();
