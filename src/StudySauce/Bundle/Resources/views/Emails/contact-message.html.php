<?php

$view->extend('StudySauceBundle:Emails:layout.html.php');

$view['slots']->start('message'); ?>
<strong>Name: </strong><?php print $view->escape($name); ?><br />
<strong>Email: </strong><?php print $view->escape($email); ?><br />
<strong>Message: </strong>
<hr />
<?php print $message; ?>
<?php $view['slots']->stop(); ?>
