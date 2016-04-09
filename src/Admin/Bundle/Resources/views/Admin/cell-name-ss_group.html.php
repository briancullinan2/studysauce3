<?php
use StudySauce\Bundle\Entity\Group;

/** @var Group $ss_group */
?>
<label class="input">
    <input type="text" name="name" value="<?php print $view->escape($ss_group->getName()); ?>"/>
</label>
<span class="count"><?php print $ss_group->getGroupPacks()->count() + $ss_group->getSubgroups()->count(); ?></span>
