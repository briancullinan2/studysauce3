<?php

$view->extend('StudySauceBundle:Emails:layout.html.php');

$view['slots']->start('message'); ?>
    We have all had that experience where we were so bored reading that we suddenly realized we had been reading for 10 minutes and had no idea we covered. What a waste of time!<br /><br />
    Learn how to read actively and better remember what you are reading.
<?php $view['slots']->stop(); ?>