<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Now what?
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<p>Communication is the key!  Outline and agree upon expectations for your student.  Remember that it is ok if it feels a little uncomfortable.  Then hold the student accountable for achieving the goals he/she create.</p>
<p>Set up regular check-ins (try to talk at least once every week or two).  Let your student be transparent about his/her struggles and successes during the conversations.  They need the outlet.</p>
<p>In general, just be there for your student.  There will undoubtedly be times throughout school that he/she will need you!</p>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>
<ul class="dialog-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
<a href="#close" class="btn btn-primary" data-dismiss="modal">Done</a>
<?php $view['slots']->stop() ?>
