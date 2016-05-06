<?php
use StudySauce\Bundle\Entity\User;

$view->extend('AdminBundle:Admin:dialog.html.php');

/** @var User $user */
$user = $app->getUser();
$isDemo = $user == 'anon.' || !is_object($user) || $user->hasRole('ROLE_GUEST') || $user->hasRole('ROLE_DEMO');



$view['slots']->start('modal-body') ?>
<p>put message here</p>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>
<a href="#close" class="btn" data-dismiss="modal">Cancel</a>
<a href="#submit" class="btn btn-primary" data-dismiss="modal">Confirm</a>
<?php $view['slots']->stop() ?>
