<?php $view->extend('AdminBundle:Admin:dialog.html.php');

$view['slots']->start('modal-header') ?>
<a href="<?php print $view['router']->generate('checkin'); ?>">Check in to start tracking your study hours</a>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>
<a href="<?php print $view['router']->generate('checkin'); ?>" class="btn btn-primary">Checkin</a>
<?php $view['slots']->stop() ?>

