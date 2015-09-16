<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Why an accountability partner?
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<p>Research shows that simply writing down your goals makes you more likely to achieve them.  Having an accountability partner takes this to a new level.  We all have ups and downs in school and finding someone to help motivate and challenge you along the way can be invaluable.</p>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>
<ul class="dialog-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
<a href="#partner-advice-3" class="btn btn-primary" data-toggle="modal">Next</a>
<?php $view['slots']->stop() ?>
