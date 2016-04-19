<?php
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

/** @var User|Group $ss_group */
$entityIds = [];

$packs = $ss_group->getGroupPacks()->filter(function (Pack $p) {return !$p->getDeleted();})->toArray();
$users = $ss_group->getUsers()->toArray();
foreach($ss_group->getSubgroups()->toArray() as $g) {
    /** @var Group $g */
    if($g->getDeleted()) {
        continue;
    }
    foreach($g->getGroupPacks()->toArray() as $p) {
        if(!in_array($p, $packs)) {
            $packs[] = $p;
        }
    }
    foreach($g->getUsers()->toArray() as $u) {
        if(!in_array($u, $users)) {
            $users[] = $u;
        }
    }
}


?>

<div>
    <label><?php print count($packs); ?> packs / <?php print count($users); ?> users<?php
        if(count($ss_group->getSubgroups()->toArray()) == 0) { ?>
            <span>No subgroups</span>
        <?php }
        ?></label>
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
                })->count() + 1; // for self ?></span></a>
    <?php } ?>

</div>