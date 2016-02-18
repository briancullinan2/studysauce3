<?php
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

/** @var Group|Pack $entity */
?>

<div>
    <label class="input"
           data-options="<?php print $view->escape(json_encode(array_map(function (User $u) {
               return [
                   'value' => $u->getId(),
                   'text' => $u->getFirst() . ' ' . $u->getLast(),
                   0 => $u->getEmail()
               ];
           }, $entity->getUsers()->slice(0, 5)))); ?>"
           data-users="<?php print $view->escape(json_encode($entity->getUsers()->map(function (User $u) {
               return $u->getId();
           })->toArray())); ?>"
        data-owner="<?php print (method_exists($entity, 'getUser') && !empty($entity->getUser()) ? $entity->getUser()->getId() : ''); ?>"><input type="text" name="users" value="" placeholder="Any User / Id" /></label>
    <?php
    $i = 0;
    foreach($entity->getUsers()->slice(0, 5) as $u) {
        /** @var User $u */
        ?>
        <label class="checkbox buttons-<?php print (!method_exists($entity, 'getUser') || $u == $entity->getUser() ? 1 : 2); ?>">
            <input type="checkbox" name="users[<?php print $i; ?>][id]" value="<?php print $u->getId(); ?>" checked="checked" />
            <i></i>
            <span><?php print $u->getFirst() . ' ' . $u->getLast() . (method_exists($entity, 'getUser') ? ($u == $entity->getUser() ? ' <strong>(owner)</strong>' : '') : ''); ?></span>
            <a href="#subtract-entity" title="Remove user"></a><?php if (method_exists($entity, 'getUser') && $u != $entity->getUser()) { ?><a href="#set-owner" title="Set as owner"></a><?php } ?>
            </label>
    <?php $i++; } ?>

    <label class="checkbox template">
        <input type="checkbox" name="users[{i}][id]" value="{value}" checked="checked" />
        <i></i>
        <span>{user}</span>
        <a href="#insert-entity" title="Add user"></a><a href="#subtract-entity" title="Remove user"></a><a href="#set-owner" title="Set as owner"></a>
    </label>

    <?php if ($entity->getUsers()->count() > 5) {
        print ' <a href="#search-' . $table . ':' . $entity->getId() . '">+' . ($entity->getUsers()->count() - 5) . ' more</a>';
    }
    ?>
</div>