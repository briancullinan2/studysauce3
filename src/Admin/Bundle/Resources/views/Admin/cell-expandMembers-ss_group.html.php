<?php
use Admin\Bundle\Controller\AdminController;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

/** @var Group $ss_group */

$entityIds = [];
$users = $ss_group->getUsers()->toArray();
usort($users, function (User $p1, User $p2) {
    return strcmp($p1->getFirst() . ' ' . $p1->getLast(), $p2->getFirst() . ' ' . $p2->getLast());
});
$ids = array_map(function (User $u) {return 'ss_user-' . $u->getId();}, $users);
$packs = $ss_group->getPacks()->toArray();
usort($packs, function (Pack $p1, Pack $p2) {
    return strcmp($p1->getTitle(), $p2->getTitle());
});
$packIds = array_map(function (Pack $u) {return 'pack-' . $u->getId();}, $packs);
?>
<form action="<?php print ($view['router']->generate('save_group', ['ss_group' => ['id' => $ss_group->getId()], 'tables' => ['ss_group' => ['users']]])); ?>">

    <?php
    $groupMembersList = [
        'tables' => ['ss_user' => AdminController::$defaultMiniTables['ss_user']],
        'entities' => $users,
        'entityIds' => $ids,
        'fieldName' => 'ss_group[users]'];

    // TODO: add field name
    if(!isset($searchRequest['pack-id']) || empty($searchRequest['pack-id'])) {
        if((!isset($searchRequest['parent-ss_group-id'])
            || $ss_group->getId() != $searchRequest['parent-ss_group-id'])) {
            print $this->render('AdminBundle:Admin:cell-collection.html.php', [
                'tables' => ['pack' => AdminController::$defaultMiniTables['pack']],
                'entities' => $packs,
                'entityIds' => $packIds,
                'fieldName' => 'ss_group[packs]']);
        }
    }
    else {
        $groupMembersList['removedEntities'] = array_values(array_filter($users, function (User $user) use ($results) {return !empty($up = $user->getUserPack($results['pack'][0])) ? $up->getRemoved() : false;}));
    }
    ?>

    <?php print $this->render('AdminBundle:Admin:cell-collection.html.php', $groupMembersList); ?>
</form>
