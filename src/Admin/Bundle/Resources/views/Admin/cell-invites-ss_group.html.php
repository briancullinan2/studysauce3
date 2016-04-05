<?php
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Invite;

/** @var Group $ss_group */

?>
<label class="input"><input type="text" name="invites" value="<?php print $view->escape(join(', ', $ss_group->getInvites()->map(function (Invite $g) { return $g->getCode(); })->toArray())); ?>" /></label>