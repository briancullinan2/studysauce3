<?php
use StudySauce\Bundle\Entity\Group;
/** @var Group $ss_group */
?>
<label>
    <span>Parent group</span><br />
    <select name="parent">
        <option value="<?php print $ss_group->getId(); ?>" <?php print (empty($ss_group->getParent()) ? 'selected="selected"' : ''); ?>>No parent</option>
        <?php
        foreach($groups as $g) {
            /** @var Group $g */
            ?>
            <option value="<?php print $g->getId(); ?>" <?php print ($g == $ss_group->getParent() ? 'selected="selected"' : ''); ?>><?php print $view->escape($g->getName()); ?><?php print (!empty($g->getDescription()) ? (' (' . $view->escape($g->getDescription()) . ')') : ''); ?></option>
        <?php } ?>
    </select>
</label>
