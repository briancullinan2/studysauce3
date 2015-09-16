<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Congratulations, your study plan is ready!
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<p>If you make changes to your study plan, download the latest calendar by clicking the glowing icon in the top right corner of the screen (only appears if you make changes).<br /><br /><br /></p>
<div class="highlighted-link">
    <ul class="dialog-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    <a href="#close" data-dismiss="modal" class="more">Go to study plan</a>
</div>
<?php $view['slots']->stop();
