<?php $view->extend('AdminBundle:Admin:dialog.html.php');

$view['slots']->start('modal-header') ?>
Are you sure you want to cancel your account?
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<p>If you click yes, we will cancel your paid subscription.</p>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer'); ?>
<a href="#close" class="btn" data-dismiss="modal">No, keep my account</a>
<a href="#cancel-account" class="btn btn-primary">Yes, cancel my account</a>
<?php $view['slots']->stop() ?>

