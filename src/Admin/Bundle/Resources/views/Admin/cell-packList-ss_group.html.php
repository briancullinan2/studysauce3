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
            foreach($g->getPacks()->toArray() as $p) {
                /** @var Pack $p */
                if(!in_array($p->getId(), $countPacks)) {
                    $countPacks[count($countPacks)] = $p->getId();
                }
            }
            $added = true;
        }
    }
}
?>

<div>
    <label><?php print ($countGroups); ?> subgroups / <?php print (count($countPacks)); ?> packs / <?php print (count($countUsers)); ?> users</label>
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