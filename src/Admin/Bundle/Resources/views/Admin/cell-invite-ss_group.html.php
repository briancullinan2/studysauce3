<?php
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Invite;

/** @var Group $ss_group */

/** @var Invite $invite */
$invite = $ss_group->getInvites()->first();

?>
<label class="input">
    <input type="hidden" name="invite[id]" value="<?php print (!empty($invite) ? $invite->getId() : ''); ?>" />
    <input type="text" name="invite[code]" value="<?php print (!empty($invite) ? $invite->getCode() : ''); ?>" />
    <input type="hidden" name="invite[activated]" value="<?php print (!empty($invite) ? $invite->getActivated() : ''); ?>" />
    <input type="hidden" name="invite[group]" value="<?php print $ss_group->getId(); ?>" />
</label>