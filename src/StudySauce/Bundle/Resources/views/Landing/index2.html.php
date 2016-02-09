a<?php
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
/** @var GlobalVariables $app */

$view->extend('StudySauceBundle:Shared:layout.html.php');

$view['slots']->start('classes') ?>landing-home<?php $view['slots']->stop();

$view['slots']->start('stylesheets');

foreach ($view['assetic']->stylesheets(
    [
        '@StudySauceBundle/Resources/public/css/landing2.css',
        '@StudySauceBundle/Resources/public/css/jquery.fancybox.css',
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

foreach ($view['assetic']->javascripts(['@landing_scripts', '@StudySauceBundle/Resources/public/js/jquery.fancybox.js'],[],['output' => 'bundles/studysauce/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach; ?>
<script type="text/javascript">
    $(document).ready(function(){
        $('a:has(img)').attr('rel', 'gallery').fancybox();
    });
</script>
<?php $view['slots']->stop();

$view['slots']->start('body'); ?>
<div id="warp">
    <div class="nav">
        <div class="home active"><a href="<?php print $view['router']->generate('_welcome'); ?>">Home</a></div>
        <div class="about"><a href="<?php print $view['router']->generate('about'); ?>">About</a></div>
    </div>
    <div class="main">
        <div class="head">
            <div id="site-name" class="container navbar-header">
                <a title="Home" href="<?php print $view['router']->generate('_welcome'); ?>">
                    <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/Study_Sauce_Logo.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                        <img width="50" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
                    <?php endforeach; ?><span><strong>Study</strong> Sauce</span></a>
            </div>
            <div class="info">
                <p><a href="#contact-support" data-toggle="modal">admin@studysauce.com<br />Get in touch with us</a></p>
            </div>
        </div>
        <div class="banner">
            <h2>Study more effectively</h2>
               <ul><li><strong>Reduce study time</strong> – Study Sauce learns what you have already mastered and focuses on exactly what you need to study.</li>
                <li><strong>Remember longer</strong> – Study Sauce pushes information to you before it is forgotten, aiding long-term retention.</li>
                <li><strong>Access any time</strong> - View the insights of your education any time…long after graduation.</li></ul>
        </div>
        <div class="appstore"><a href="https://itunes.apple.com/us/app/study-sauce/id1065647027?ls=1&mt=8"><object data="/bundles/studysauce/images/Download_on_the_App_Store_Badge_US-UK_135x40.svg" type="image/svg+xml">
                </object></a></div>
        <div class="content">
            <div class="content1">
                <h2>About Study Sauce</h2>
                <p>We are obsessed with helping you remember whatever you think is important!&nbsp; <a href="<?php print $view['router']->generate('about'); ?>">Learn more</a></p>
            </div>
            <div class="content1">
                <h2>College Students</h2>
                <p>College students, please click below to access your Study Sauce accounts.&nbsp; <a href="https://course.studysauce.com/">Click here</a></p>
            </div>
            <div class="content1">
                <h2>Screen Shots</h2>
                <div class="ProImg">
                    <a href="/bundles/studysauce/images/2016-01-06 (5).png"><img src="/bundles/studysauce/images/2016-01-06 (5).png" width="55" height="55" border="0" /></a>
                    <a href="/bundles/studysauce/images/2016-01-06 (4).png"><img src="/bundles/studysauce/images/2016-01-06 (4).png" width="55" height="55" border="0" /></a>
                    <a href="/bundles/studysauce/images/2016-01-06 (3).png"><img src="/bundles/studysauce/images/2016-01-06 (3).png" width="55" height="55" border="0" /></a>
                    <a href="/bundles/studysauce/images/2016-01-06 (2).png"><img src="/bundles/studysauce/images/2016-01-06 (2).png" width="55" height="55" border="0" /></a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $view['slots']->stop();
