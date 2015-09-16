<?php
$view->extend('StudySauceBundle:Emails:layout.html.php');

$view['slots']->start('message'); ?>
Congratulations on taking the first step to improving your study effectiveness!  To get the most out of Study Sauce, we recommend a few key things to do.<br />
<br />
1. Start watching the study videos and they will guide you through the website.
<br />
2. Enter your important deadlines and we will send you reminders to stay on track.
<br />
3. Check in when you study and we will guide you through the best study techniques.<br />
<br />
As always, we are happy to help with any questions that you might have.  Just email us at admin@studysauce.com.<br />
<br />
<?php $view['slots']->stop(); ?>
