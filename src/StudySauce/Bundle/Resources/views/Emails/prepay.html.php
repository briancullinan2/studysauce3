<?php

$view->extend('StudySauceBundle:Emails:layout.html.php');

$view['slots']->start('message'); ?>
Your personal study plan has been prepaid by <?php print $user->getFirst(); ?> <?php print $user->getLast(); ?>.<br />
<br />
We started Study Sauce based on the realization that no one ever teaches students the most effective study methods.  Considering the fact that students spend 75% of college studying outside of the classroom.  We thought that was crazy.  Especially when considering the fact that we have carefully researched the science behind the most effective studying techniques and use them to build your study plan for the entire semester.<br />
<br />
Our students find that they no longer have to cram for tests and are far less stressed when they use our methods (the same methods that Jeopardy champions use to retain information - btw).  Come discover the secret sauce to studying and find out how other students like you are raising their GPAs.<br />
<br />
Please click below to finish filling out your class information, so we can finalize your study plan.<br />
<?php $view['slots']->stop(); ?>