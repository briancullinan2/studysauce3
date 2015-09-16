<?php
use Symfony\Component\HttpKernel\Controller\ControllerReference;

$view->extend('StudySauceBundle:Shared:layout.html.php');

$view['slots']->start('classes') ?>landing-home parents<?php $view['slots']->stop();

$view['slots']->start('stylesheets');

foreach ($view['assetic']->stylesheets(
    [
        '@StudySauceBundle/Resources/public/css/video.css',
        '@StudySauceBundle/Resources/public/css/scr.css',
        '@StudySauceBundle/Resources/public/css/banner.css',
        '@StudySauceBundle/Resources/public/css/features.css',
        '@StudySauceBundle/Resources/public/css/testimony.css',
        '@StudySauceBundle/Resources/public/css/footer.css',
        '@StudySauceBundle/Resources/public/css/landing.css'
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
<?php endforeach; ?>
    <!-- Facebook Conversion Code for Facebook -->
    <script>(function() {
            var _fbq = window._fbq || (window._fbq = []);
            if (!_fbq.loaded) {
                var fbds = document.createElement('script');
                fbds.async = true;
                fbds.src = '//connect.facebook.net/en_US/fbds.js';
                var s = document.getElementsByTagName('script')[0];
                s.parentNode.insertBefore(fbds, s);
                _fbq.loaded = true;
            }
        })();
        window._fbq = window._fbq || [];
        window._fbq.push(['track', '6008770260529', {'value':'0.00','currency':'USD'}]);
    </script>
    <noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?ev=6008770260529&amp;cd[value]=0.00&amp;cd[currency]=USD&amp;noscript=1" /></noscript>
<?php $view['slots']->stop();

$view['slots']->start('body');
echo $view->render('StudySauceBundle:Landing:parent-video.html.php');
echo $view->render('StudySauceBundle:Landing:parent-scr.html.php');
echo $view->render('StudySauceBundle:Landing:parent-banner.html.php');
echo $view->render('StudySauceBundle:Landing:parent-features.html.php');
echo $view->render('StudySauceBundle:Landing:testimony.html.php');
$view['slots']->stop();

$view['slots']->start('sincludes');
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'student-invite']));
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'student-invite-confirm']));
$view['slots']->stop();