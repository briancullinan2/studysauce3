<?php
use Admin\Bundle\Controller\AdminController;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\Entity\UserPack;

$context = !empty($context) ? $context : jQuery($this);
$expandMembers = $context->find('.expandMembers');
if($expandMembers->length == 0) {
    // skip for now, this will be activated on hover
    return;
}

/** @var Pack $pack */

$entityIds = [];
/** @var User[] $users */
$users = [];
foreach($pack->getUserPacks()->toArray() as $up) {
    /** @var UserPack $up */
    $users[count($users)] = $up->getUser();
}
if(isset($request['ss_group-id']) && !empty($group = $request['ss_group-id'])) {
    $groupUsers = [];
    foreach($users as $u) {
        $include = false;
        foreach($u->getGroups()->toArray() as $g) {
            /** @var Group $g */
            if($g->getId() == $group) {
                $include = true;
                break;
            }
        }
        if($include) {
            $groupUsers[count($groupUsers)] = $u;
        }
    }
    $users = $groupUsers;
}
else if (isset($results['ss_group'])) {
    // displaying the list of users not in subgroups which are displayed right below this row
    $groupNames = [];
    foreach($results['ss_group'] as $g) {
        /** @var Group $g */
        $groupNames[count($groupNames)] = $g->getName();
    }
    $groupUsers = [];
    foreach($users as $u) {
        $include = true;
        foreach($u->getGroups()->toArray() as $g) {
            /** @var Group $g */
            if(in_array($g->getName(), $groupNames)) {
                $include = false;
                break;
            }
        }
        if($include) {
            $groupUsers[count($groupUsers)] = $u;
        }
    }
    $users = $groupUsers;
}
AdminController::sortByFields($users, ['first', 'last']);
$removed = [];
foreach($users as $u) {
    if(!empty($up = $pack->getUserPack($u)) && $up->getRemoved()) {
        $removed[count($removed)] = $u;
    }
}

$ids = [];
foreach($users as $u) {
    $ids[count($ids)] = implode('', ['ss_user-' , $u->getId()]);
}

?>
<form action="<?php print ($view['router']->generate('command_save', ['pack' => ['id' => $pack->getId()], 'tables' => ['pack' => ['id', 'userPacks'], 'user_pack' => ['user', 'pack'], 'ss_user' => ['id']]])); ?>">
    <?php print ($view->render('AdminBundle:Admin:cell-collection.html.php', [
        'tables' => ['ss_user' => AdminController::$defaultMiniTables['ss_user']],
        'entities' => $users,
        'entityIds' => $ids,
        'fieldName' => 'pack[userPacks]',
        'removedEntities' => $removed])); ?>
</form>
