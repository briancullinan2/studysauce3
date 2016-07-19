<?php
use Admin\Bundle\Controller\AdminController;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;


$context = !empty($context) ? $context : jQuery($this);
$expandMembers = $context->find('.expandMembers');
if($expandMembers->length == 0) {
    // skip for now, this will be activated on hover
    return;
}

/** @var Group $ss_group */

$entityIds = [];
/** @var User[] $countUsers */
$subGroups = [$ss_group->getId()];
$countGroups = 0;
// TODO: use IDs so it works in javascript too
$countUsers = [];
$users = [];
$countPacks = [];
$packIds = [];
/** @var Pack[] $packs */
$packs = [];
foreach($ss_group->getUsers()->toArray() as $u) {
    /** @var User $u */
    if(!in_array($u->getId(), $countUsers)) {
        $countUsers[count($countUsers)] = $u->getId();
        $users[count($users)] = $u;
    }
}
foreach($ss_group->getPacks()->toArray() as $p) {
    /** @var Pack $p */
    if(!in_array($p->getId(), $countPacks) && $p->getStatus() != 'DELETED') {
        $countPacks[count($countPacks)] = $p->getId();
        $packs[count($packs)] = $p;
        $packIds[count($packIds)] = implode('', ['pack-' , $p->getId()]);
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
                    $users[count($users)] = $u;
                }
            }
            foreach($g->getPacks()->toArray() as $p) {
                /** @var Pack $p */
                if(!in_array($p->getId(), $countPacks) && $p->getStatus() != 'DELETED') {
                    $countPacks[count($countPacks)] = $p->getId();
                    $packs[count($packs)] = $p;
                    $packIds[count($packIds)] = implode('', ['pack-' , $p->getId()]);
                }
            }
            $added = true;
        }
    }
}
AdminController::sortByFields($users, ['first', 'last']);
$ids = [];
$removed = [];
foreach($users as $u) {
    $ids[count($ids)] = implode('', ['ss_user-' , $u->getId()]);
    if(!empty($request['pack-id']) && !empty($up = $u->getUserPack($results['pack'][0])) && $up->getRemoved()) {
        $removed[count($removed)] = $u;
    }
}
AdminController::sortByFields($packs, ['title']);
?>
<form action="<?php print ($view['router']->generate('save_group', ['ss_group' => ['id' => $ss_group->getId()], 'tables' => ['ss_group' => ['users']]])); ?>">
    <?php

    $groupMembersList = [
        'tables' => ['ss_user' => AdminController::$defaultMiniTables['ss_user']],
        'entities' => $users,
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
