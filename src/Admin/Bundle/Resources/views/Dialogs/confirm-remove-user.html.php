<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Are you sure you want to delete <span id="remove-user-name">this user</span>?
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<p>If you click yes, it will be gone FOREVER!</p>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer'); ?>
<a href="#close" class="btn" data-dismiss="modal">No, don't delete</a>
<a href="#remove-user" class="btn btn-primary" data-dismiss="modal">Yes, delete user</a>
<?php $view['slots']->stop() ?>

