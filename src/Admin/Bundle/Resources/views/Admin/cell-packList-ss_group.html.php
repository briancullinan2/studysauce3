<?php
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

/** @var Group $ss_group */
$entityIds = [];

$usersGroupsPacks = $ss_group->getUsersPacksGroupsRecursively();
$users = $usersGroupsPacks[0];
$packs = $usersGroupsPacks[1];
$groups = $usersGroupsPacks[2];
?>

<div>
    <label><?php print (implode('', [count($groups) , ' subgroups'])); ?> / <?php print (count($packs)); ?> packs / <?php print (count($users)); ?> users</label>
    <?php
    foreach ($ss_group->getSubgroups()->toArray() as $g) {
        /** @var Group $g */
        if($g->getDeleted()) {
            continue;
        }

        $subGroupCount = 0;
        foreach($g->getSubgroups()->toArray() as $c) {
            /** @var Card $c */
            if(!$c->getDeleted()) {
                $subGroupCount += 1;
            }
        }

        ?>
        <a href="<?php print ($view['router']->generate('groups_edit', ['group' => $g->getId()])); ?>" class="pack-list"><?php print ($view->escape($g->getName())); ?>
            <span><?php print ($subGroupCount); ?></span></a>
    <?php } ?>
</div>