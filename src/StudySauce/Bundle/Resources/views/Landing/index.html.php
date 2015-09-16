<?php
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
/** @var GlobalVariables $app */

$view->extend('StudySauceBundle:Shared:layout.html.php');

$view['slots']->start('classes') ?>landing-home<?php $view['slots']->stop();

$view['slots']->start('stylesheets');

foreach ($view['assetic']->stylesheets(
    [
        '@StudySauceBundle/Resources/public/css/video.css',
        '@StudySauceBundle/Resources/public/css/scr.css',
        '@StudySauceBundle/Resources/public/css/banner.css',
        '@StudySauceBundle/Resources/public/css/testimony.css',
        '@StudySauceBundle/Resources/public/css/footer.css'
    ],
    [],
    ['output' => 'bundles/studysauce/css/*.css']
) as $url):
    ?>
    <link rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
<?php endforeach;

$view['slots']->stop();

$view['slots']->start('javascripts');

foreach ($view['assetic']->javascripts(['@landing_scripts'],[],['output' => 'bundles/studysauce/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;

$view['slots']->stop();

$view['slots']->start('body');
echo $view->render('StudySauceBundle:Landing:video.html.php');
echo $view->render('StudySauceBundle:Landing:benefits.html.php');
echo $view->render('StudySauceBundle:Landing:testimony.html.php');
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'schedule-demo']), ['strategy' => 'sinclude']);
$view['slots']->stop();
