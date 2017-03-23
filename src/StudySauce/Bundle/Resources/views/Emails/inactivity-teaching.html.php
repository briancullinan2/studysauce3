<?php

$view->extend('StudySauceBundle:Emails:layout.html.php');

$view['slots']->start('message'); ?>
    Certain types of classes require that you have a deeper understanding of the material.  When rote memorization fails, use a technique called "teach to learn."
<?php $view['slots']->stop(); ?>