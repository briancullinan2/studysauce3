<?php
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;

/** @var Group $ss_group */
?>
<label class="input">
    <input type="text" name="name" value="<?php print $view->escape($ss_group->getName()); ?>"/>
</label>
