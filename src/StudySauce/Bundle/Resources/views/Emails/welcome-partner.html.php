<?php
$view->extend('StudySauceBundle:Emails:layout.html.php');

$view['slots']->start('message'); ?>
Thank you for joining Study Sauce on behalf of your student.  To get the most out of our service, we recommend a few key things to get started.<br />
<br />
1. Set up regular check in meetings with your student to discuss the highs and lows of school.  We recommend weekly meetings.
<br />
2. Lean on your experience to help students avoid common pitfalls.  Try to find a good balance between being supportive and challenging your student during your weekly meetings.
<br />
3. Consider upgrading your student to a premium Study Sauce account to help take his/her studying to the next level.<br />
<br />
As always, we are happy to help with any questions that you might have. Just email us at admin@studysauce.com.<br />
<br />
<?php $view['slots']->stop(); ?>
