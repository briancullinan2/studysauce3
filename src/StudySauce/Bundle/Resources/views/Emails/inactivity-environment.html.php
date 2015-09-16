<?php

$view->extend('StudySauceBundle:Emails:layout.html.php');

$view['slots']->start('message'); ?>
    More often than not, listening to music makes it more difficult to study.  Even though you think you are in the groove when you listen to your favorite playlist, the science shows you may be making it much harder on yourself.<br /><br />
    Learn how you can set up a study environment that makes you more effective.
<?php $view['slots']->stop(); ?>