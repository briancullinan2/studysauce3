<?php
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

/** @var User|Group|Pack $entity */
?>

<div>
    <?php foreach ($groups as $i => $g) {
        /** @var Group $g */
        ?>
        <label class="checkbox">
            <input type="checkbox" name="groups"
                   value="<?php print $g->getId(); ?>" <?php print ($entity->hasGroup($g->getName())
                ? 'checked="checked"'
                : ''); ?> /><i></i><span><?php print $g->getName(); ?></span>
        </label>
        <?php if (method_exists($entity, 'getGroup')) { ?>
            <label class="radio">
                <input type="radio" name="group-<?php print $entity->getId(); ?>"
                       value="<?php print $g->getId(); ?>" <?php print ($entity->getGroup() == $g ? 'checked="checked"' : ''); ?> /><i></i><strong>(owner)</strong>
            </label>
        <?php }
    }?>
</div>