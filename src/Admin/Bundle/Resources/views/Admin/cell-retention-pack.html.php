<?php

$user = $app->getUser();
$retention = \StudySauce\Bundle\Controller\PacksController::getRetention($pack, $user);
$retentionCount = count(array_filter($retention, function ($r) {return $r[2];}));

?>
<label><?php print $retentionCount; ?></label>