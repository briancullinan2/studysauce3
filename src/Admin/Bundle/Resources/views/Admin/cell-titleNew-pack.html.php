<?php
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;


/** @var Pack $pack */
/** @var User $user */
$user = $app->getUser(); ?>

<label><?php print ($pack->isNewForChild($user) ? '<strong>New </strong>' : ''); ?><span><?php print $view->escape($pack->getTitle()); ?></span></label>