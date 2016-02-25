<?php
use StudySauce\Bundle\Entity\User;

$view->extend('StudySauceBundle:Emails:layout.html.php');

/** @var User $user */

$view['slots']->start('message'); ?>
We just wanted to let you know that [sponsor] has just added a new study pack to ["your" for a parent or [child first name]"'s"] Study Sauce account.<br />
<?php $view['slots']->stop(); ?>
Body:
Hello [parent first name] -



(left side)
[sponsor logo]

(right side)
[pack name]
[number of cards in pack] cards

We hope you enjoy the new study material!

Thank you,
The Study Sauce Team

P.S. If you have any questions, please reach out to us at admin@studysauce.com.