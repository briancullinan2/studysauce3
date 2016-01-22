<?php
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Templating\TimedPhpEngine;

/** @var $view TimedPhpEngine */
/** @var $user User */

$view->extend('StudySauceBundle:Shared:layout.html.php');

$view['slots']->start('classes') ?>landing-home<?php $view['slots']->stop();

$view['slots']->start('stylesheets');

foreach ($view['assetic']->stylesheets(
    [
        '@StudySauceBundle/Resources/public/css/landing2.css',
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

$view['slots']->start('body'); ?>
<div id="warp">
    <div class="nav">
        <div class="home"><a href="<?php print $view['router']->generate('_welcome'); ?>">Home</a></div>
        <div class="about active"><a href="<?php print $view['router']->generate('about'); ?>">About</a></div>
    </div>
    <div class="main">
        <div class="head">
            <div id="site-name" class="container navbar-header">
                <a title="Home" href="<?php print $view['router']->generate('_welcome'); ?>">
                    <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/Study_Sauce_Logo.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                        <img width="50" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
                    <?php endforeach; ?><span><strong>Study</strong> Sauce</span></a>
            </div>
            <h3>Our Mission:</h3>
            <p>We are obsessed with helping you remember whatever you think is important!  You spend so much time studying, so why not use the most effective methods and remember for the long term?</p>
            <h3>The Science:</h3>
            <p>Study Sauce is based on three core findings of study research.</p>
            <ul><li>Spaced Repetition - if you don’t revisit what you study periodically, you will not retain it.</li>
                <li>Self-testing - forcing yourself to answer questions has been proven over and over again to help you learn.</li>
                <li>Interleaving - mixing up the different topics you study helps you remember everything better.</li></ul>
            <p> The day’s study cards are specifically calculated and chosen for you to go through just before you forget.  They are calculated using The Forgetting Curve which was discovered by Hermann Ebbinghaus in the late 1800s.  The material you get right will come back to you at a later date than the material you haven’t yet mastered.  This way, you are focusing on exactly what you need to learn.  Additionally, by going back through the material periodically, you are creating a deeper memory which will help you retain the information for the long term. </p>
            <h3>Special Thanks:</h3>
            <p>We wanted to express our gratitude to the dedicated staff of Archway Classical Academy - Scottsdale.  In particular, thank you to Mr. Kuhlman, Mrs. Armstrong, Mrs. Gonzalez, Mrs. Taff, Ms. Rolenz, Miss. Cohen, Mrs. Miller, and Mrs. Dufresne.</p>
        </div>
    </div>
</div>
<?php $view['slots']->stop(); ?>
