<?php $view->extend('AdminBundle:Admin:dialog.html.php');

$view['slots']->start('modal-header') ?>
Note is blank
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<p>This note has been left blank.  Would you like to discard?</p>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>
<a href="#close" class="btn" data-dismiss="modal">No, save this note</a>
<a href="#discard-note" class="btn btn-primary" data-dismiss="modal">Yes, delete this note</a>
<?php $view['slots']->stop() ?>

