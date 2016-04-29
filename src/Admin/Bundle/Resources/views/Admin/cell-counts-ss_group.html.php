<?php
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

/** @var Group $ss_group */
list($users, $packs) = $ss_group->getUsersPacksGroupsRecursively();

?>
<label class="input">
    <span><?php print count($users); ?></span>
</label>
<label class="input">
    <span><?php print count($packs); ?></span>
</label>
