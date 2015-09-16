<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Step 2 - Now let's customize your study plan
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<p>We have now created the prework study sessions that you will need throughout the week.  Drag the colored boxes onto your calendar to create your plan.<br/><br/></p>
<p>Note - Prework sessions should be within 24 hours before your class.<br/><br/><br/><br/></p>
<div class="highlighted-link">
    <ul class="dialog-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    <a href="#add-prework" data-dismiss="modal" class="more">Add pre-work</a>
</div>
<?php $view['slots']->stop();
