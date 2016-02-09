<?php
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\User;

/** @var User $ss_user */
$time = !empty($ss_user->getLastVisit()) ? $ss_user->getLastVisit() : $ss_user->getCreated();
?>
<div data-timestamp="<?php print (!empty($time) ? $time->getTimestamp() : 0); ?>"><?php print (!empty($time) ? $time->format('j M H:i') : ''); ?></div>
