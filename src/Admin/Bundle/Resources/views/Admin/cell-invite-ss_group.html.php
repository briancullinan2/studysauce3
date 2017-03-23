<?php
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Invite;

/** @var Group $ss_group */

/** @var Invite[] $invite */
$invite = $ss_group->getInvites()->toArray();

?>
<label class="input">
    <input type="text" name="invites" value="<?php print (!empty($invite) ? $invite[0]->getCode() : ''); ?>" />
</label>