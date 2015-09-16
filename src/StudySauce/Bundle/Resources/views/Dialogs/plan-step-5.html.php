<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Step 5 - Final tweaks
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<p>Before download this study plan to your calendar, you have one more chance to make some adjustments.  For example, we strongly suggest adding a location for your study sessions.  You can do that by simply double clicking any of the events on your calendar.<br/><br/></p>
<p>Note - Don't worry, you can always make changes to your plan later.</p>
<br/><br/><br/><br/>
<div class="highlighted-link">
    <ul class="dialog-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    <a href="#make-final" data-dismiss="modal" class="more">Make final adjustments</a>
</div>
<?php $view['slots']->stop();
