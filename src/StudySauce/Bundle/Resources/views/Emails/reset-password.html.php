<?php

$view->extend('StudySauceBundle:Emails:layout.html.php');

$view['slots']->start('message'); ?>
    Your password has been reset.  Use the link below to set a new one.<br />
    <br />
<?php $view['slots']->stop();
