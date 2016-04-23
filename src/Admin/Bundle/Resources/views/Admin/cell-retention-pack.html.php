<?php

use StudySauce\Bundle\Entity\Pack;

/** @var Pack $pack */

if(!empty($pack->getUser()) && $pack->getUser()->getId() == $searchRequest['ss_user-id']) {
    $user = $pack->getUser();
}
else {
    $user = $pack->getUserById($searchRequest['ss_user-id']);
}
$retentionCount = 0;
if(!empty($user)) {
    $retention = \StudySauce\Bundle\Controller\PacksController::getRetention($pack, $user);
    $retentionCount = count(array_filter($retention, function ($r) {
        return $r[2];
    }));
}
?>
<label><?php print $retentionCount; ?></label>