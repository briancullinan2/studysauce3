<?php
use Symfony\Component\HttpKernel\Controller\ControllerReference;

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(['@StudySauceBundle/Resources/public/css/home.css',],[],['output' => 'bundles/studysauce/css/*.css']) as $url):?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
$view['slots']->stop();

$view['slots']->start('body'); ?>

<div class="panel-pane" id="home">
    <?php
    ?>
</div>

<?php $view['slots']->stop();

$view['slots']->start('sincludes');

$view['slots']->stop();
