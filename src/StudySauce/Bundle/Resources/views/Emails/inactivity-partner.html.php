<?php

$view->extend('StudySauceBundle:Emails:layout.html.php');

$view['slots']->start('message'); ?>
    Sure, there are a ton of things you don't miss about living with your parents, but they may have been helpful in keeping you on top of your goals and deadlines.<br /><br />
    Students we work with kept bringing up the fact that they no longer had someone holding them accountable for their education.  In response, we built the accountability partner feature into Study Sauce.  Accountabliity partners can work with you to root you on during school and help make sure you achieve what you set out to.
<?php $view['slots']->stop(); ?>