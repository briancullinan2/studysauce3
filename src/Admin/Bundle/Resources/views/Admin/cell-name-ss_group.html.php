<?php
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;

/** @var Group $ss_group */
?>
<label class="input">
    <input type="text" name="name" value="<?php print $view->escape($ss_group->getName()); ?>"/>
</label>
<span class="count"><?php print $ss_group->getSubgroups()->map(function (Group $g) {
            return $g->getDeleted() ? 0 : $g->getSubgroups()->count() + 1;})->count(); ?></span>
