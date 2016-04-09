<?php
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;

/** @var Group $ss_group */
?>
<label class="input">
    <input type="text" name="name" value="<?php print $view->escape($ss_group->getName()); ?>"/>
</label>
<span class="count"><?php print $ss_group->getGroupPacks()->filter(function (Pack $g) {
            return $g->getStatus() != 'DELETED';})->count()
        + $ss_group->getSubgroups()->filter(function (Group $g) {
            return !$g->getDeleted();})->count(); ?></span>
