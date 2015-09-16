<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Step 2 - Now let's customize your study plan
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<p>Finally, add your free study sessions.  These can go anywhere you want and are used when you need extra time to complete a paper, work on a project, or catch up on studying.<br/><br/><br/><br/></p>
<div class="highlighted-link">
    <ul class="dialog-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    <a href="#add-free-study" data-dismiss="modal" class="more">Add free study</a>
</div>
<?php $view['slots']->stop();
