<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Congratulations, your study plan is ready!
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<p>Your study plan is now connected to Google Calendar.  Any changes you make in Study Sauce or in your calendar will sync automatically.<br /><br /></p>
<p>It may take up to 15 minutes for your plan to sync to first time.<br /><br /><br /></p>
<div class="highlighted-link">
    <ul class="dialog-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    <a href="#close" data-dismiss="modal" class="more">Go to study plan</a>
</div>
<?php $view['slots']->stop();
