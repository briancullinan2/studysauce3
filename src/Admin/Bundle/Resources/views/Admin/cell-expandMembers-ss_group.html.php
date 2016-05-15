<?php
use Admin\Bundle\Controller\AdminController;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

/** @var Group $ss_group */

$entityIds = [];
/** @var User[] $users */
$users = $ss_group->getUsers()->toArray();
AdminController::sortByFields($users, ['first', 'last']);
$ids = [];
$removed = [];
foreach($users as $u) {
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
        'entities' => $users,
        'entityIds' => $ids,
        'fieldName' => 'ss_group[users]'];

    // TODO: add field name
    if(!isset($request['pack-id']) || empty($request['pack-id'])) {
        if((!isset($request['parent-ss_group-id'])
            || $ss_group->getId() != $request['parent-ss_group-id'])) {
            print ($view->render('AdminBundle:Admin:cell-collection.html.php', [
                'tables' => ['pack' => AdminController::$defaultMiniTables['pack']],
                'entities' => $packs,
                'entityIds' => $packIds,
                'fieldName' => 'ss_group[packs]']));
        }
    }
    else {
        $groupMembersList['removedEntities'] = $removed;
    }
    ?>

    <?php print ($view->render('AdminBundle:Admin:cell-collection.html.php', $groupMembersList)); ?>
</form>
