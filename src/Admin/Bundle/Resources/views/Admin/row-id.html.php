<?php
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\User;

/** @var User|Group $entity */
$time = method_exists($entity, 'getModified') && !empty($entity->getModified()) ? $entity->getModified() : $entity->getCreated();
?>
<div data-timestamp="<?php print $time->getTimestamp(); ?>"><?php print $time->format('j M H:i'); ?></div>
