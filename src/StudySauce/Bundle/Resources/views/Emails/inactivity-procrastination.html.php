<?php

$view->extend('StudySauceBundle:Emails:layout.html.php');

$view['slots']->start('message'); ?>
    We all do it at times, but there are ways to minimize procrastination.<br /><br />
    Our procrastination video is packed with tips and tricks to help end your favorite pastime.
<?php $view['slots']->stop(); ?>