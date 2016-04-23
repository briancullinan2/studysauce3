<?php

/** @var Pack $pack */
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\Pack;

$total = $pack->getCards()->filter(function (Card $c) {return !$c->getDeleted();})->count();
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
if($total > 0) {
    $mastery = round(($total - $retentionCount) / $total * 100.0);
}
else {
    $mastery = 0;
}

?>
<label style="padding-left:<?php print $mastery; ?>%">&nbsp;<?php print $mastery; ?></label>
