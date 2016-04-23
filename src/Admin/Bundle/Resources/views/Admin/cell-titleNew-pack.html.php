<?php
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;


/** @var Pack $pack */
/** @var User $user */
if(!empty($pack->getUser()) && $pack->getUser()->getId() == $searchRequest['ss_user-id']) {
    $user = $pack->getUser();
}
else {
    $user = $pack->getUserById($searchRequest['ss_user-id']);
}
$isNew = true;
if(!empty($user)) {
    $isNew = $pack->isNewForChild($user);
}
?>

<label><?php print ($isNew ? '<strong>New </strong>' : ''); ?><span><?php print $view->escape($pack->getTitle()); ?></span></label>