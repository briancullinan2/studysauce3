<?php

$view->extend('StudySauceBundle:Emails:layout.html.php');

$view['slots']->start('message'); ?>
    If you are like us, you have probably misplaced your notes at some point and frantically searched for them.<br /><br />
    Study Sauce works with Evernote to not only put all of your study notes in one place, but we also back them up in the cloud so that you don't have to worry about them disappearing when your computer crashes.<br />
<?php $view['slots']->stop(); ?>