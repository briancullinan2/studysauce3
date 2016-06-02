<?php
use Admin\Bundle\Controller\AdminController;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

/** @var Group $ss_group */

$entityIds = [];
/** @var User[] $countUsers */
$subGroups = [$ss_group->getId()];
$countGroups = 0;
// TODO: use IDs so it works in javascript too
$countUsers = $ss_group->getUsers()->toArray();
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
                if(!in_array($u, $countUsers)) {
                    $countUsers[count($countUsers)] = $u;
                }
            }
            foreach($g->getPacks()->toArray() as $p) {
                /** @var Pack $p */
                if(!in_array($p, $countPacks)) {
                    $countPacks[count($countPacks)] = $p;
                }
            }
            $added = true;
        }
    }
}
AdminController::sortByFields($countUsers, ['first', 'last']);
$ids = [];
$removed = [];
foreach($countUsers as $u) {
    $ids[count($ids)] = implode('', ['ss_user-' , $u->getId()]);
    if(!empty($request['pack-id']) && !empty($up = $u->getUserPack($results['pack'][0])) && $up->getRemoved()) {
        $removed[count($removed)] = $u;
    }
}
/** @var Pack[] $packs */
$packs = $ss_group->getPacks()->toArray();
AdminController::sortByFields($packs, ['title']);
$packIds = [];
foreach($packs as $p) {
    $packIds[count($packIds)] = implode('', ['pack-' , $p->getId()]);
}
?>
<form action="<?php print ($view['router']->generate('save_group', ['ss_group' => ['id' => $ss_group->getId()], 'tables' => ['ss_group' => ['users']]])); ?>">
    <?php

    $groupMembersList = [
        'tables' => ['ss_user' => AdminController::$defaultMiniTables['ss_user']],
        'entities' => $countUsers,
        'entityIds' => $ids,
        'fieldName' => 'ss_group[users]'];

    // TODO: add field name
    if(!isset($request['pack-id']) || empty($request['pack-id'])) {
        if((!isset($request['parent-ss_group-id'])
            || $ss_group->getId() != $request['parent-ss_group-id'])) {
            print ($view->render('AdminBundle:Admin:cell-collection.html.php', [
                'tables' => ['pack' => AdminController::$defaultMiniTables['pack']],
                'headers' => ['pack' => 'Study Packs'],
                'entities' => $packs,
                'entityIds' => $packIds,
                'fieldName' => 'ss_group[packs]']));
        }
    }
    else {
        $groupMembersList['removedEntities'] = $removed;
    }

    print ($view->render('AdminBundle:Admin:cell-collection.html.php', $groupMembersList)); ?>
</form>
