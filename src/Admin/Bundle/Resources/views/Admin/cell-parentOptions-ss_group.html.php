<?php
use StudySauce\Bundle\Entity\Group;
/** @var Group $ss_group */


foreach ($groups as $g) {
    /** @var Group $g */
    ?>
    <option
        value="<?php print ($g->getId()); ?>" <?php print ($g == $ss_group->getParent() ? 'selected="selected"' : ''); ?>><?php print ($view->escape($g->getName())); ?></option>
    <?php
    $subGroups = $g->getSubgroups()->toArray();
    if ($subGroups > 0) { ?>
        <optgroup label="<?php print ($view->escape($g->getName())); ?> Group">
            <?php print ($view->render('AdminBundle:Admin:cell-parentOptions-ss_group.html.php', ['groups' => $subGroups, 'ss_group' => $ss_group])); ?>
        </optgroup>
        <?php
    }
}
