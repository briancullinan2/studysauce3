<?php

$view->extend('StudySauceBundle:Emails:layout.html.php');

$view['slots']->start('message'); ?>
    You know that nightmare you have about forgetting an important exam and showing up completely unprepared?<br /><br />
    Stop having it by setting up reminders for your important deadlines.<br /><br />
    Study Sauce will email you ahead of time to warn you when something important is approaching.  We will also deliver the message straight to your phone if you have a study plan connected.
<?php $view['slots']->stop(); ?>