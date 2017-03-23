<?php $view->extend('AdminBundle:Admin:dialog.html.php');

$view['slots']->start('modal-header') ?>
Thank you.
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<p>We have sent the email invitation.  You will be notified once the invitation is accepted.</p>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>
<a href="#close" class="btn btn-primary" data-dismiss="modal">Close</a>
<?php $view['slots']->stop() ?>

