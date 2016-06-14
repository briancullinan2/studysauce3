<?php
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

/** @var Group $ss_group */

$subGroups = [$ss_group->getId()];
$countGroups = 0;
$countUsers = [];
$countPacks = [];
$groupPacks = [];
foreach($ss_group->getUsers()->toArray() as $u) {
    /** @var User $u */
    if(!in_array($u->getId(), $countUsers)) {
        $countUsers[count($countUsers)] = $u->getId();
    }
}
foreach($ss_group->getGroupPacks()->toArray() as $p) {
    /** @var Pack $p */
    if(!in_array($p->getId(), $countPacks) && $p->getStatus() != 'DELETED') {
        $countPacks[count($countPacks)] = $p->getId();
        $groupPacks[count($groupPacks)] = $p;
    }
}
$added = true;
while($added) {
    $added = false;
    foreach($results['allGroups'] as $g) {
        /** @var Group $g */
        if(!empty($g->getParent())
            && in_array($g->getParent()->getId(), $subGroups)
            && !in_array($g->getId(), $subGroups)) {
            $subGroups[count($subGroups)] = $g->getId();
            $countGroups += 1;
            foreach($g->getUsers()->toArray() as $u) {
                /** @var User $u */
                if(!in_array($u->getId(), $countUsers)) {
                    $countUsers[count($countUsers)] = $u->getId();
                }
            }
            foreach($g->getGroupPacks()->toArray() as $p) {
                /** @var Pack $p */
                if(!in_array($p->getId(), $countPacks) && $p->getStatus() != 'DELETED') {
                    $countPacks[count($countPacks)] = $p->getId();
                    $groupPacks[count($groupPacks)] = $p;
                }
            }
            $added = true;
        }
    }
}
?>

<div>
    <label><?php print ($countGroups); ?> subgroups / <?php print (count($groupPacks)); ?> packs / <?php print (count($countUsers)); ?> users</label>
    <?php
    foreach ($groupPacks as $g) {
        /** @var Pack $g */
        if($g->getDeleted()) {
            continue;
        }

        $subGroupCount = 0;
        foreach($g->getUsers()->toArray() as $c) {
            /** @var Card $c */
            //if(!$c->getDeleted()) {
                $subGroupCount += 1;
            //}
        }

        ?>
        <a href="<?php print ($view['router']->generate('packs_edit', ['pack' => $g->getId()])); ?>" class="pack-list"><?php print ($view->escape($g->getTitle())); ?>
            <span><?php print ($subGroupCount); ?></span></a>
    <?php } ?>
</div>