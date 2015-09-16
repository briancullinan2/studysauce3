<?php
$view->extend('StudySauceBundle:Emails:layout.html.php');

$view['slots']->start('message'); ?>
Log in to Study Sauce to see your student's accomplishment.<br />
<br />
<?php $view['slots']->stop(); ?>
