<?php

$view['slots']->set('greeting', 'Hello ' . $user->getFirst() . ',');

$view->extend('StudySauceBundle:Emails:layout.html.php');

$view['slots']->start('message'); ?>

<?php $view['slots']->stop();
