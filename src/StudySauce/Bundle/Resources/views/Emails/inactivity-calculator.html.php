<?php

$view->extend('StudySauceBundle:Emails:layout.html.php');

$view['slots']->start('message'); ?>
    Do you know your current grades?<br /><br />
    What do you need to make on your next test?<br /><br />
    Are you on track to hit your GPA goal this term?<br />
<?php $view['slots']->stop(); ?>