<?php
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

/** @var Group|Pack $entity */
?>

<div>
    <?php print implode(', ', array_map(function (User $u) {return $u->getFirst() . ' ' . $u->getLast();}, $entity->getUsers()->slice(0, 5))) . ($entity->getUsers()->count() > 5 ? (', + ' . $entity->getUsers()->count() - 5 . ' more'): ''); ?>
</div>