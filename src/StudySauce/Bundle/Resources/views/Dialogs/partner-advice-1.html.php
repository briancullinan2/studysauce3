<?php $view->extend('AdminBundle:Admin:dialog.html.php');

$view['slots']->start('modal-header') ?>
Welcome to Study Sauce
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<p>Thank you for agreeing to help your student<?php print (isset($account->field_first_name['und'][0]['value']) ? (', ' . $account->field_first_name['und'][0]['value']) : ''); ?>.  Click the button below for a few tips on how to be a great accountability partner.</p>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>
<ul class="dialog-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
<a href="#partner-advice-2" class="btn btn-primary" data-toggle="modal">Next</a>
<?php $view['slots']->stop() ?>
