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
    print $view['actions']->render(new ControllerReference('StudySauceBundle:Course:widget'));
    print $view['actions']->render(new ControllerReference('StudySauceBundle:Goals:widget'));
    print $view['actions']->render(new ControllerReference('StudySauceBundle:Deadlines:widget'));
    print $view['actions']->render(new ControllerReference('StudySauceBundle:Metrics:widget'));
    print $view['actions']->render(new ControllerReference('StudySauceBundle:Plan:widget'));
    print $view['actions']->render(new ControllerReference('StudySauceBundle:Checkin:widget'));
    ?>
</div>

<?php $view['slots']->stop();

$view['slots']->start('sincludes');
if($showBookmark)
{
    echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'bookmark']));
}
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:sdsMessages'), ['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'checklist']), ['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'timer-expire']), ['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'mozart']), ['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'checkin-empty']), ['strategy' => 'sinclude']);
$view['slots']->stop();
