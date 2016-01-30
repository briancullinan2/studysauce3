<?php
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

/** @var Pack $pack */
?>

<div>
    <?php foreach ($groups as $i => $g) {
        /** @var Group $g */
        ?>
        <label class="checkbox">
            <input type="checkbox" name="groups"
                   value="<?php print $g->getId(); ?>" <?php print ($pack->hasGroup($g->getName())
                ? 'checked="checked"'
                : ''); ?> /><i></i><span><?php print $view->escape($g->getName()); ?></span>
        </label>
        <?php if (method_exists($pack, 'getGroup')) { ?>
            <label class="checkbox">
                <input type="checkbox" name="group"
                       value="<?php print $g->getId(); ?>" <?php print ($pack->getGroup() == $g ? 'checked="checked"' : ''); ?> /><i></i><strong>(owner)</strong>
            </label>
        <?php }
    }?>
</div>