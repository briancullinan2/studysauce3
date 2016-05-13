<?php
use Admin\Bundle\Controller\AdminController;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\Entity\UserPack;

/** @var Pack $pack */

$entityIds = [];
$users = $pack->getUserPacks()->map(function (UserPack $up) {return $up->getUser();});
$ids = $users->map(function (User $u) {return 'ss_user-' . $u->getId();})->toArray();
if(isset($searchRequest['ss_group-id']) && !empty($group = $searchRequest['ss_group-id'])) {
    $users = $users->filter(function (User $u) use ($group) {
        return $u->getGroups()->filter(function (Group $g) use ($group) {return $g->getId() == $group;})->count() > 0;});
}
else if (isset($results['ss_group'])) {
    // displaying the list of users not in subgroups which are displayed right below this row
    $users = $users->filter(function (User $u) use ($results) {
        return $u->getGroups()->filter(function (Group $g) use ($results) {return in_array($g, $results['ss_group']);})->count() == 0;});
}
$sorted = $users->toArray();
usort($sorted, function (User $p1, User $p2) {
    return strcmp($p1->getFirst() . ' ' . $p1->getLast(), $p2->getFirst() . ' ' . $p2->getLast());
});
?>
<form action="<?php print (!empty($group)
    ? $view['router']->generate('save_group', ['ss_group' => ['id' => $searchRequest['ss_group-id']]])
    : $view['router']->generate('packs_create', ['pack' => ['id' => $pack->getId()]])); ?>">
    <?php print $this->render('AdminBundle:Admin:cell-collection.html.php', [
        'tables' => ['ss_user' => AdminController::$defaultMiniTables['ss_user']],
        'entities' => $sorted,
        'entityIds' => $ids,
        'removedEntities' => array_values($users->filter(function (User $user) use ($pack) {return !empty($up = $user->getUserPack($pack)) ? $up->getRemoved() : false;})->toArray())]); ?>
</form>
