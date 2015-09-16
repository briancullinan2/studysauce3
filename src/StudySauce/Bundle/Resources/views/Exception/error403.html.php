<?php
use StudySauce\Bundle\Entity\User;

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

/** @var User $user */
$user = $app->getUser();
$groups = $user->getGroups()->toArray();
$roles = $user->getRoles();

$view['slots']->start('body'); ?>
<div class="panel-pane" id="error403">
    <div class="pane-content">
        <?php if ($view['security']->isGranted('ROLE_PREVIOUS_ADMIN')) { ?>
            Access Denied. <a href="<?php print $view['router']->generate('command_control'); ?>?_switch_user=_exit">Click here to EXIT.</a>
        <?php }
        else { ?>
            Access Denied. <a href="<?php print $view['router']->generate('logout'); ?>">Click here to Log Out.</a>
        <?php } ?>
    </div>
</div>
<?php $view['slots']->stop();
