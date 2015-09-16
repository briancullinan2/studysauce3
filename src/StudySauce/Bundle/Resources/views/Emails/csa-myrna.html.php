<?php

$view['slots']->set('greeting', 'Hello ' . $user->getFirst() . ',');

/** @var \Symfony\Bundle\FrameworkBundle\Templating\TimedPhpEngine $view */
$view->extend('StudySauceBundle:Emails:layout.html.php');

$view['slots']->start('message'); ?>
I hope the new term is off to a great start. As you recall, one of the conditions for the CSA scholarship is use of the study tool that we have provided you - Study Sauce. If you haven't logged in recently, you will be surprised by how much has changed on the website. The most notable change is the creation of a video course to help you improve your study habits. We think you will enjoy the course as it covers the most frequently discussed areas of studying (test-taking strategies, studying for different types of tests, studying in groups, etc).<br />
<br />
Completion of the full course is a requirement for the spring semester. <strong>Please make sure that you have completed Levels 1-3 of the course by February 20th.</strong>
<br/>
<?php
$view['slots']->stop();

$view['slots']->start('salutation');
?>
Good luck this semester!<br/>
 - <?php print str_replace('CSA ', '', substr($group->getName(), 0, strpos($group->getName(), '\'') ?: strlen($group->getName()))); ?><br />
<br />
P.S. If you have any questions, please either contact me, or send an email directly to Stephen Houghton (stephen@studysauce.com).<br/>
<?php
$view['slots']->stop();

