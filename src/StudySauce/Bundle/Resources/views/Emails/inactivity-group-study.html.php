<?php

$view->extend('StudySauceBundle:Emails:layout.html.php');

$view['slots']->start('message'); ?>
    Sometimes studying in groups tends to devolve into social hour.  It doesn't have to be that way.<br /><br />
    There are a few simple things that you can do to make your study group more effective.
<?php $view['slots']->stop(); ?>