<?php $view->extend('AdminBundle:Admin:dialog.html.php');

$view['slots']->start('modal-header') ?>
<a href="<?php print $view['router']->generate('schedule'); ?>">Click here to set up your class schedule and get
    started.</a>
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
Then checkin whenever you study using the Check in tab.
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>
<a href="<?php print $view['router']->generate('schedule'); ?>" class="btn btn-primary">Edit schedule</a>
<?php $view['slots']->stop() ?>

