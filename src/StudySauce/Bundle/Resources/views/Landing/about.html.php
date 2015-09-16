<?php
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Templating\TimedPhpEngine;

/** @var $view TimedPhpEngine */
/** @var $user User */

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(
    [
        '@StudySauceBundle/Resources/public/css/about.css'
    ],
    [],
    ['output' => 'bundles/studysauce/css/*.css']
) as $url):
    ?>
    <link rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');

$view['slots']->stop();

$view['slots']->start('body'); ?>
<div class="panel-pane" id="about">
    <div class="pane-content">
        <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/about_us_background_compressed.png'],[],['output' => 'bundles/studysauce/images/*']) as $url): ?>
            <img src="<?php echo $view->escape($url) ?>" alt="LOGO"/>
        <?php endforeach; ?>
        <div class="rightify">
            <h2>Our Mantra</h2>

            <h3>We teach students how to study<br>...because no one else does</h3>

            <h2>Our story</h2>

            <p>We started this company based on the realization that after 20+ years of school, we had no idea how to
                study properly. Sure, we found a method that worked for us to get by, but it got us thinking about how
                much better we could have done. How would our experience (and performance) have changed if anyone along
                the way had taught us how to study? When we started looking into the science on studying, we realized
                just how little work has been done in the area. Our mission is to fix that by helping our students learn
                how to use the most effective study methods to achieve their academic goals. Feel free to contact us, we
                would love to hear from you!</p>

            <h2>Our contact info</h2>
            <div>
                <a target="_blank" href="https://www.facebook.com/pages/Study-Sauce/519825501425670?ref=stream">&nbsp;</a>
                <a href="https://plus.google.com/115129369224575413617/about">&nbsp;</a>
                <a href="https://twitter.com/StudySauce">&nbsp;</a>
                <a href="<?php print $view['router']->generate('privacy'); ?>">&nbsp;</a>
            </div>
            <div class="highlighted-link"><br /><a href="<?php print $view['router']->generate('_welcome'); ?>" class="more">Go home</a></div>
        </div>
    </div>
</div>
<?php $view['slots']->stop(); ?>
