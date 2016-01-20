<?php
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

/** @var Group|Pack $entity */
?>

<div>
    <label class="input"><input type="text" name="users" value="" placeholder="Any User / Id" /></label>
    <?php print implode(' , ', array_map(function (User $u) use ($entity) {
            return $u->getFirst() . ' ' . $u->getLast() . (method_exists($entity, 'getUser') ? ($u == $entity->getUser() ? ' <strong>(owner)</strong>' : '') : '');
        }, $entity->getUsers()->slice(0, 5)));
    if ($entity->getUsers()->count() > 5) {
        print ' <a href="#search-packs:' . $entity->getId() . '">+' . ($entity->getUsers()->count() - 5) . ' more</a>';
    }
    ?>
</div>