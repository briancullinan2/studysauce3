<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Are you sure you want to cancel <span id="cancel-user-name">this user</span>?
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<p>If you click yes, they will no longer be automatically billed and the PAID role will be removed.</p>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer'); ?>
<a href="#close" class="btn" data-dismiss="modal">No, don't cancel</a>
<a href="#cancel-user" class="btn btn-primary" data-dismiss="modal">Yes, cancel payment</a>
<?php $view['slots']->stop() ?>

