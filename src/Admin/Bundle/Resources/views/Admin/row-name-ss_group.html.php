<?php
use StudySauce\Bundle\Entity\Group;

/** @var Group $ss_group */
?>
<div class="group-name">
<label class="input">
    <input type="text" name="groupName" value="<?php print $view->escape($ss_group->getName()); ?>"/><br/>
</label>
<label class="input">
    <textarea name="description"><?php print $view->escape($ss_group->getDescription()); ?></textarea></label>
</div>