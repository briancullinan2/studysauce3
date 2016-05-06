<?php $view->extend('AdminBundle:Admin:dialog.html.php');

$view['slots']->start('modal-header') ?>
    Get the App!
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
Download the app from the app store:

Or click here to continue using the app:
<div class="highlighted-link">
    <a href="#" class="more">Open App</a>
</div>
<?php $view['slots']->stop();
