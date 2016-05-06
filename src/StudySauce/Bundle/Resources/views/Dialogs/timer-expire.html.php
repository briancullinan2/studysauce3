<?php $view->extend('AdminBundle:Admin:dialog.html.php');

 $view['slots']->start('modal-header') ?>
Another session is in the books
<?php $view['slots']->stop();

 $view['slots']->start('modal-body') ?>
<p>You have completed another session, congrats.</p>
<?php $view['slots']->stop();
