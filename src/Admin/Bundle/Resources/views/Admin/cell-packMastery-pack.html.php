<?php

/** @var Pack $pack */
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\Pack;

$user = $app->getUser();
$total = $pack->getCards()->filter(function (Card $c) {return !$c->getDeleted();})->count();
$retention = \StudySauce\Bundle\Controller\PacksController::getRetention($pack, $user);
$retentionCount = count(array_filter($retention, function ($r) {return $r[2];}));
if($total > 0) {
    $mastery = ($total - $retentionCount) / $total;
}
else {
    $mastery = 0;
}

?>
<label style="padding-left:<?php print $mastery; ?>%">&nbsp;<?php print $mastery; ?></label>
