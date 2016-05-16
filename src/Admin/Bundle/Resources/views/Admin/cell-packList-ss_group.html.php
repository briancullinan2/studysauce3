<?php
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

/** @var Group $ss_group */

$subGroups = [];
$countGroups = 0;
$countUsers = 0;
$countPacks = 0;
foreach($results['allGroups'] as $g) {
    /** @var Group $g */
    if(!empty($g->getParent()) && isset($request['ss_group-id']) && ($g->getParent()->getId() == $request['ss_group-id'] || in_array($g->getParent()->getId(), $subGroups))) {
        $subGroups[count($subGroups)] = $g->getId();
        $countGroups += 1;
        $countUsers += count($g->getUsers()->toArray());
        $countPacks += count($g->getPacks()->toArray());
    }
}
?>

<div>
    <label><?php print ($countGroups); ?> subgroups / <?php print ($countPacks); ?> packs / <?php print ($countUsers); ?> users</label>
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