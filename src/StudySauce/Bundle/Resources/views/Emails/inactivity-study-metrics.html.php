<?php

$view->extend('StudySauceBundle:Emails:layout.html.php');

$view['slots']->start('message'); ?>
    Students often have no idea how much time they should spend studying for each class.  If you don't estimate well, you either waste a ton of time or you don't make the grades you want.<br /><br />
    Tracking your study hours can help you understand how much effort is required to get the grades you hope for.
<?php $view['slots']->stop(); ?>