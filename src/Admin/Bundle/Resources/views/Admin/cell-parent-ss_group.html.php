<?php
use StudySauce\Bundle\Entity\Group;
/** @var Group $ss_group */
?>
<label>
    <select name="parent">
        <option value="<?php print $ss_group->getId(); ?>" <?php print (empty($ss_group->getParent()) ? 'selected="selected"' : ''); ?>>No parent</option>
        <?php
        $options = function () {};
        $options = function ($groups) use ($ss_group, $view, &$options) {
            foreach ($groups as $g) {
                /** @var Group $g */
                ?>
                <option
                    value="<?php print $g->getId(); ?>" <?php print ($g == $ss_group->getParent() ? 'selected="selected"' : ''); ?>><?php print $view->escape($g->getName()); ?></option>
                <?php
                if ($g->getSubgroups()->count() > 0) { ?>
                    <optgroup label="<?php print $view->escape($g->getName()); ?> Group">
                        <?php $options($g->getSubgroups()->toArray()); ?>
                    </optgroup>
                <?php
                }
            }
        };
        $options(array_values(array_filter($groups, function (Group $g) {return empty($g->getParent());})));
        ?>
    </select>
</label>
