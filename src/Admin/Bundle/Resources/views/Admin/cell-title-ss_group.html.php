<?php
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

/** @var Group $ss_group */
$subGroups = [$ss_group->getId()];
$countUsers = [];
foreach($ss_group->getUsers()->toArray() as $u) {
    /** @var User $u */
    if((!in_array($u->getId(), $countUsers))) {
        $countUsers[count($countUsers)] = $u->getId();
    }
}
$countPacks = [];
foreach($ss_group->getGroupPacks()->toArray() as $p) {
    /** @var Pack $p */
    if((!in_array($p->getId(), $countPacks)) && $p->getStatus() != 'DELETED') {
        $countPacks[count($countPacks)] = $p->getId();
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
            foreach($g->getUsers()->toArray() as $u) {
                /** @var User $u */
                if((!in_array($u->getId(), $countUsers))) {
                    $countUsers[count($countUsers)] = $u->getId();
                }
            }
            foreach($g->getGroupPacks()->toArray() as $p) {
                /** @var Pack $p */
                if((!in_array($p->getId(), $countPacks)) && $p->getStatus() != 'DELETED') {
                    $countPacks[count($countPacks)] = $p->getId();
                }
            }
            $added = true;
        }
    }
}

if (isset($request['parent-ss_group-id']) && $ss_group->getId() == $request['parent-ss_group-id']) {
    print ($view->render('AdminBundle:Admin:cell-label.html.php', ['fields' => ['All users (not in subgroups below)', 0, 0]]));
} else { ?>
    <a href="<?php print ($view['router']->generate('groups_edit', ['group' => $ss_group->getId()])); ?>">
    <?php print ($view->render('AdminBundle:Admin:cell-label.html.php', ['fields' => [$ss_group->getName(), count($countUsers), count($countPacks)]])); ?>
    </a>
<?php }