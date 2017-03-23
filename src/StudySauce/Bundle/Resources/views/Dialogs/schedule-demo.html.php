<?php
use StudySauce\Bundle\Entity\User;

$view->extend('AdminBundle:Admin:dialog.html.php');

/** @var User $user */
$user = $app->getUser();
$isDemo = $user == 'anon.' || !is_object($user) || $user->hasRole('ROLE_GUEST') || $user->hasRole('ROLE_DEMO');


$view['slots']->start('modal-header') ?>
Schedule a demo
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<form action="<?php print $view['router']->generate('contact_send'); ?>" method="post">
    <div class="first-name">
        <label class="input"><span>First name</span><input name="first-name" type="text" value="<?php print ($isDemo ? '' : $view->escape($user->getFirst())); ?>"></label>
    </div>
    <div class="last-name">
        <label class="input"><span>Last name</span><input name="last-name" type="text" value="<?php print ($isDemo ? '' : $view->escape($user->getLast())); ?>"></label>
    </div>
    <div class="company">
        <label class="input"><span>Company</span><input name="company" type="text" value=""></label>
    </div>
    <div class="email">
        <label class="input"><span>Your email</span><input name="your-email" type="email" value="<?php print ($isDemo || substr($user->getEmail(), -strlen('@example.org')) == '@example.org' ? '' : $view->escape($user->getEmail())); ?>"></label>
    </div>
    <div class="phone">
        <label class="input"><span>Phone</span><input name="phone" type="text" value=""></label>
    </div>
    <div class="highlighted-link">
        <button type="submit" value="#submit-contact" class="more">Send</button>
    </div>
</form>
<?php $view['slots']->stop();

