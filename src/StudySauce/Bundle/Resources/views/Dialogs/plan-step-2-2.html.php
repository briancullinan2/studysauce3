<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Step 2 - Now let's customize your study plan
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<p>Next, add your spaced repetition study sessions.  Drag the colored boxes onto your calendar to create your plan.<br/><br/></p>
<p>Note - Spaced repetition sessions should be within 24 hours after your class.<br/><br/><br/><br/></p>
<div class="highlighted-link">
    <ul class="dialog-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    <a href="#add-spaced-repetition" data-dismiss="modal" class="more">Add spaced-repetition</a>
</div>
<?php $view['slots']->stop();
