<?php
use StudySauce\Bundle\Entity\User;

$view->extend('StudySauceBundle:Emails:layout.html.php');

/** @var User $user */

$view['slots']->start('message'); ?>
<?php print $user->getFirst(); ?> wants to invite you to become an accountability partner.<br />
<br />
Research shows that simply writing down goals makes them more likely to be achieved. Having an accountability partner greatly increases the probability of achievement. All students have ups and downs in school and finding someone to help motivate and challenge them along the way can be invaluable.<br />
<br />
You can work out the specific expectations with <?php print $user->getFirst(); ?>, but a good accountability partner tends to have the following attributes:<br />
<ul style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555;">
    <li>Will challenge a student (needs more than just encouragement)</li>
    <li>Will celebrate student successes</li>
    <li>Is emotionally invested in the student's education</li>
    <li>Commits to regular communication about academics</li>
</ul>
<?php $view['slots']->stop(); ?>
