<?php
use StudySauce\Bundle\Entity\Invite;
use StudySauce\Bundle\Entity\ParentInvite;
use StudySauce\Bundle\Entity\StudentInvite;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\EventListener\InviteListener;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;
use Symfony\Bundle\FrameworkBundle\Templating\TimedPhpEngine;
/** @var GlobalVariables $app */
/** @var $view TimedPhpEngine */
/** @var Invite $invite */
$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(['@StudySauceBundle/Resources/public/css/account.css'], [], ['output' => 'bundles/studysauce/css/*.css']) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(['@StudySauceBundle/Resources/public/js/register.js'], [], ['output' => 'bundles/studysauce/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('body'); ?>

<div class="panel-pane" id="reset">
    <div class="pane-content">
        <h2><?php if(!empty($token)) { ?>Set a new password<?php } else { ?>Reset your password<?php } ?></h2>
        <form action="<?php print $view['router']->generate('password_reset'); ?>" method="post">
        <div class="email <?php print (!empty($token) ? 'read-only' : ''); ?>">
            <label class="input"><input type="text" name="email" placeholder="Email" value="<?php print $email; ?>"></label>
        </div>
        <?php if(!empty($token)) { ?>
            <div class="password">
                <label class="input"><input type="password" name="pass" placeholder="New password" value=""></label>
            </div>
            <div class="confirm-password">
                <label class="input"><input type="password" name="newPass" placeholder="Confirm password" value=""></label>
            </div>
        <?php } ?>
        <input type="hidden" name="token" value="<?php echo $token; ?>"/>
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>"/>
        <div class="form-actions highlighted-link invalid">
            <button type="submit" value="#reset-password" class="more"><?php print (empty($token) ? 'Reset password' : 'Set password'); ?></button>
        </div>
        </form>
        <div class="reset-sent-message">
            <h3>Your password recovery email has been sent.  Please click the link in the email to set a new password.</h3>
            <div class="highlighted-link">
                <a href="<?php print $view['router']->generate('_welcome'); ?>" class="more">Go home</a>
            </div>
        </div>
    </div>
</div>

<?php $view['slots']->stop(); ?>
