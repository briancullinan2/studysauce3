<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Are you sure you want to delete this notebook
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>
<a href="#close" class="btn" data-dismiss="modal">No, cancel</a>
<a href="#confirm-delete-notebook" class="btn btn-primary">Yes, delete</a>
<?php $view['slots']->stop() ?>

