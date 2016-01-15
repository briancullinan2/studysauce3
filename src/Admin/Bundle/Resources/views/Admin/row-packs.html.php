<?php
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

/** @var User|Group $entity */
?>

<div>
    <?php print implode(', ', array_map(function (Pack $p) {return $p->getTitle();}, $entity->getPacks()->slice(0, 5))) . ($entity->getPacks()->count() > 5 ? (', + ' . $entity->getPacks()->count() - 5 . ' more'): ''); ?>
</div>