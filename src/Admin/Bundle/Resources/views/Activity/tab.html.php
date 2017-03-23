<?php
use StudySauce\Bundle\Entity\Partner;
use StudySauce\Bundle\Entity\User;

/** @var User $user */
$user = $app->getUser();

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets'); ?>
<link type="text/css" rel="stylesheet" href="<?php print $view['router']->generate('_welcome'); ?>bundles/admin/js/vis/vis.css"/>
<?php foreach ($view['assetic']->stylesheets(['@AdminBundle/Resources/public/css/activity.css'],[],['output' => 'bundles/admin/css/*.css']) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts'); ?>
<?php foreach ($view['assetic']->javascripts(
    [
        '@AdminBundle/Resources/public/js/vis/vis.js',
        '@AdminBundle/Resources/public/js/activity.js'
    ],
    [],
    ['output' => 'bundles/admin/js/*.js']
) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach; ?>
<script type="text/javascript">
    window.initialDates = <?php print json_encode($visits); ?>;
</script>
<?php $view['slots']->stop();

$view['slots']->start('body'); ?>
<div class="panel-pane" id="activity">
    <div class="pane-content">
        <div class="search">
            <label class="input"><input name="search" type="text" value="" placeholder="Search"/></label>
        </div>
        <div class="range">
            <label class="input"><input type="text" name="range" value="" placeholder="Date range"/></label>
            <div></div>
        </div>
        <div id="visit-timeline"></div>
    </div>
</div>
<?php $view['slots']->stop();


