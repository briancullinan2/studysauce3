<?php
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Templating\TimedPhpEngine;

/** @var $view TimedPhpEngine */
/** @var $user User */
$user = $app->getUser();
$isDemo = $user == 'anon.' || !is_object($user) || $user->hasRole('ROLE_GUEST') || $user->hasRole('ROLE_DEMO');

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
<div class="panel-pane" id="contact">
    <div class="pane-content">
        <div class="centrify">
            <h2>Contact us</h2>
            <p>If you have any questions at all, please contact us. &nbsp;We would love to hear from you! &nbsp;
                We want to help you to get the most out of your study time and your comments and feedback are important to us.</p>
            <div class="name">
                <label class="input"><span>Your name</span><input name="your-name" type="text" value="<?php print ($isDemo ? '' : $view->escape($user->getFirst() . ' ' . $user->getLast())); ?>"></label>
            </div>
            <div class="email">
                <label class="input"><span>Your email</span><input name="your-email" type="email" value="<?php print ($isDemo || substr($user->getEmail(), -strlen('@example.org')) == '@example.org' ? '' : $view->escape($user->getEmail())); ?>"></label>
            </div>
            <div class="message">
                <label class="input"><span>Message</span><textarea placeholder="" cols="60" rows="2"></textarea></label>
            </div>
            <div class="highlighted-link">
                <a href="#submit-contact" class="more">Send</a>
            </div>
        </div>
    </div>
</div>
<?php $view['slots']->stop(); ?>
