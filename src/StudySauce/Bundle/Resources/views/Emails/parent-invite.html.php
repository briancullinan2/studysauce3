<?php
use StudySauce\Bundle\Entity\User;

$view->extend('StudySauceBundle:Emails:layout.html.php');

/** @var User $user */

$view['slots']->start('message'); ?>
<?php print $user->getFirst(); ?> has asked for your help to sponsor a premium Study Sauce account.<br />
<br />
We started Study Sauce based on the realization that no one ever teaches students the most effective study methods. We thought that was crazy when considering the fact that students spend up to 75% of their time outside the classroom.<br />
<br />
We have carefully researched the most effective study techniques and have incorporated the leading science into our online service to teach our students how to employ the best study methods.<br />
<br />
Click on the below link to find out more about how we can help your student become a better studier.<br />
<?php $view['slots']->stop(); ?>
