<?php

/** @var Pack $pack */
use StudySauce\Bundle\Controller\PacksController;
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\UserPack;

$cardCount = 0;
foreach($pack->getCards()->toArray() as $c) {
    /** @var Card $c */
    if(!$c->getDeleted()) {
        $cardCount += 1;
    }
}

if(!empty($pack->getUser()) && $pack->getUser()->getId() == $request['ss_user-id']) {
    $user = $pack->getUser();
}
else {
    $user = $pack->getUserById($request['ss_user-id']);
}
$retentionCount = 0;
foreach($results['ss_user'][0]->getUserPacks()->toArray() as $i => $up) {
    /** @var UserPack $up */
    if ($up->getPack()->getId() == $pack->getId()) {
        foreach ($up->getRetention() as $i => $r) {
            if($r[2]) {
                $retentionCount += 1;
            }
        }
    }
}

if($cardCount > 0) {
    $mastery = round(($cardCount - $retentionCount) / $cardCount * 100.0);
}
else {
    $mastery = 0;
}

?>
<label style="padding-left:<?php print ($mastery); ?>%;width:<?php print ($mastery); ?>%;">&nbsp;<?php print ($mastery); ?></label>
