<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Why me?
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<p>Your student chose you because he/she believes that you will:</p>
<ul>
    <li>Challenge him/her. This requires more than just encouragement.</li>
    <li>Will celebrate his/her successes.</li>
    <li>Be emotionally invested in his/her education.</li>
    <li>Continue to be someone that he/she trusts.</li>
</ul>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>
<ul class="dialog-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
<a href="#partner-advice-4" class="btn btn-primary" data-toggle="modal">Next</a>
<?php $view['slots']->stop() ?>
