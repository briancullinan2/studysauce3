<?php
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

/** @var Group $ss_group */
$entityIds = [];

list($users, $packs, $groups) = $ss_group->getUsersPacksGroupsRecursively();
?>

<div>
    <label><?php print count($groups) . ' subgroups'; ?> / <?php print count($packs); ?> packs / <?php print count($users); ?> users</label>
    <?php
    foreach ($ss_group->getSubgroups()->toArray() as $g) {
        /** @var Group $g */
        if($g->getDeleted()) {
            continue;
        }
        ?>
        <a href="<?php print $view['router']->generate('groups_edit', ['group' => $g->getId()]); ?>" class="pack-list"><?php print $g->getName(); ?>
            <span><?php print $g->getSubgroups()->filter(function (Group $p) {
                    return !$p->getDeleted();
                })->count(); // for self ?></span></a>
    <?php } ?>
</div>