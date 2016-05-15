<?php

use StudySauce\Bundle\Controller\PacksController;
use StudySauce\Bundle\Entity\Pack;

/** @var Pack $pack */

if(!empty($pack->getUser()) && $pack->getUser()->getId() == $request['ss_user-id']) {
    $user = $pack->getUser();
}
else {
    $user = $pack->getUserById($request['ss_user-id']);
}
$retentionCount = 0;
if(!empty($user)) {
    $retention = PacksController::getRetention($pack, $user);
    foreach($retention as $r) {
        if($r[2]) {
            $retentionCount += 1;
        }
    }
}
?>
<label><?php print ($retentionCount); ?></label>