<?php
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Invite;

/** @var Group $ss_group */

/** @var Invite[] $invite */
$invite = $ss_group->getInvites()->toArray();

?>
<label class="input">
    <input type="hidden" name="invite[id]" value="<?php print (!empty($invite) ? $invite[0]->getId() : ''); ?>" />
    <input type="text" name="invite[code]" value="<?php print (!empty($invite) ? $invite[0]->getCode() : ''); ?>" />
    <input type="hidden" name="invite[activated]" value="<?php print (!empty($invite) ? $invite[0]->getActivated() : ''); ?>" />
    <input type="hidden" name="invite[group]" value="<?php print ($ss_group->getId()); ?>" />
</label>