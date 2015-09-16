<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Drag events within 24 hours of class
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<p>The science of studying shows that students have the best retention when preparing for a class within 24 hours before, and studying the same material in class within 24 hours after the class.<br/><br/><br/><br/></p>
<div class="highlighted-link">
    <a href="#close" data-dismiss="modal" class="more">Don't show this again</a>
</div>
<?php $view['slots']->stop();
