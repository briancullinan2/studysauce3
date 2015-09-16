<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Thanks!
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<h3>Your student has been notified.</h3>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>
<a href="#close" class="btn" data-dismiss="modal">Close</a>
<?php $view['slots']->stop() ?>

