<?php

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(['@StudySauceBundle/Resources/public/css/buy.css'],[],['output' => 'bundles/studysauce/css/*.css']) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(['@StudySauceBundle/Resources/public/js/buy.js'],[],['output' => 'bundles/studysauce/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('body'); ?>
    <div class="panel-pane" id="thanks">
        <div class="pane-content clearfix">
            <h1>Thank you!</h1>
            <h2>We are excited to start working with you and will be in touch shortly.<br />
                <br />- The Study Sauce Team</h2>
            <a href="<?php print $view['router']->generate('home'); ?>">Go home</a>
        </div>
    </div>
<?php $view['slots']->stop();
