<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Are you sure you want to reset <span id="reset-user-name">this user</span>?
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<p>If you click yes, this user's password will be removed and a one time link will be sent to their email address.</p>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer'); ?>
<a href="#close" class="btn" data-dismiss="modal">No, don't reset</a>
<a href="#reset-password" class="btn btn-primary" data-dismiss="modal">Yes, reset password</a>
<?php $view['slots']->stop() ?>

