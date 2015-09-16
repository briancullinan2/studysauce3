<?php
use Symfony\Component\HttpKernel\Controller\ControllerReference;

$view->extend('StudySauceBundle:Shared:layout.html.php');

$view['slots']->start('classes') ?>landing-home partners<?php $view['slots']->stop();

$view['slots']->start('stylesheets');

foreach ($view['assetic']->stylesheets(
    [
        '@StudySauceBundle/Resources/public/css/video.css',
        '@StudySauceBundle/Resources/public/css/scr.css',
        '@StudySauceBundle/Resources/public/css/banner.css',
        '@StudySauceBundle/Resources/public/css/testimony.css',
        '@StudySauceBundle/Resources/public/css/footer.css',
        '@StudySauceBundle/Resources/public/css/landing.css',
    ],
    [],
    ['output' => 'bundles/studysauce/css/*.css']
) as $url):
    ?>
    <link rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
<?php endforeach;

$view['slots']->stop();

$view['slots']->start('javascripts');

foreach ($view['assetic']->javascripts(
    [
        '@landing_scripts'
    ],
    [],
    ['output' => 'bundles/studysauce/js/*.js']
) as $url):
    ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;

$view['slots']->stop();

$view['slots']->start('body');
echo $view->render('StudySauceBundle:Landing:partner-video.html.php');
echo $view->render('StudySauceBundle:Landing:partner-features.html.php');
echo $view->render('StudySauceBundle:Landing:testimony.html.php');
$view['slots']->stop();
