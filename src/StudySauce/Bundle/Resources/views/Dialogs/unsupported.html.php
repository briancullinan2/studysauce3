<?php
use StudySauce\Bundle\Entity\User;

$view->extend('AdminBundle:Admin:dialog.html.php');

/** @var User $user */
$user = $app->getUser();
$isDemo = !is_object($user) || $user->hasRole('ROLE_GUEST') || $user->hasRole('ROLE_DEMO');

$view['slots']->start('modal-header') ?>
Your browser is not supported
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<p>Please choose from the browsers below to download the latest version.  Thank you!</p>
<p>
    <a href="https://www.google.com/chrome/browser/">&nbsp;</a>
    <a href="https://www.mozilla.org/en-US/firefox/new/">&nbsp;</a>
    <a href="http://support.apple.com/downloads/#safari">&nbsp;</a>
<?php $view['slots']->stop();
