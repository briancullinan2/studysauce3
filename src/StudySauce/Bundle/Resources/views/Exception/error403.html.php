<?php


$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('body'); ?>
<div class="panel-pane" id="error403">
    <div class="pane-content">
        <?php if ($view['security']->isGranted('ROLE_PREVIOUS_ADMIN')) { ?>
            Access Denied. <a href="<?php print $view['router']->generate('command'); ?>?_switch_user=_exit">Click here to EXIT.</a>
        <?php }
        else if (!empty($user) && !$user->hasRole('ROLE_GUEST')) { ?>
            Access Denied. <a href="<?php print $view['router']->generate('logout'); ?>">Click here to Log Out.</a>
        <?php }
        else { ?>
            Access Denied. <a href="<?php print $view['router']->generate('login'); ?>">Click here to Log In.</a>
        <?php } ?>
    </div>
</div>
<?php $view['slots']->stop();
