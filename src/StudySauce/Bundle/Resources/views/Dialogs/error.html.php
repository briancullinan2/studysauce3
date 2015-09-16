<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Sorry an error occurred.
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
{error}
<?php $view['slots']->stop();
