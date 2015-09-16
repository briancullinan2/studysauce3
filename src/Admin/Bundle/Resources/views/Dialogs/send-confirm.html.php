<?php
use StudySauce\Bundle\Entity\Group;

$view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Are you sure you really want to send ( <strong class="count"></strong> ) email(s)?
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>

<?php $view['slots']->stop();

$view['slots']->start('modal-footer'); ?>
<a href="#confirm-send" class="btn btn-primary" data-dismiss="modal">Send</a>
<?php $view['slots']->stop() ?>

