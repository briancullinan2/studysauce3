<?php
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;


/** @var Pack $pack */
/** @var User $user */
$user = $app->getUser();
$isNew = $pack->isNewForChild($user);
?>

<label><?php print ($isNew ? '<strong>New </strong>' : ''); ?><span><?php print $view->escape($pack->getTitle()); ?></span></label>