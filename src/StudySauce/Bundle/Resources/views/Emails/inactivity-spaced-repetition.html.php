<?php

$view->extend('StudySauceBundle:Emails:layout.html.php');

$view['slots']->start('message'); ?>
    Learn the science behind how your brain commits information to memory and why you can't seem to remember what you study.<br /><br />
    Learn how to study in a way that helps you actually remember things.
<?php $view['slots']->stop(); ?>