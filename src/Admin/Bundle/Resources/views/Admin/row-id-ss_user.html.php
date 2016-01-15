<?php
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\User;

/** @var User $ss_user */
$time = !empty($ss_user->getLastVisit()) ? $ss_user->getLastVisit() : $ss_user->getCreated();
?>
<div data-timestamp="<?php print $time->getTimestamp(); ?>"><?php print $time->format('j M H:i'); ?></div>
