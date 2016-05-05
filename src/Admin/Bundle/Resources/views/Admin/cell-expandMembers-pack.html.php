<?php
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
?>
<form action="<?php print (!empty($group)
    ? $view['router']->generate('save_group', ['groupId' => $searchRequest['ss_group-id'], 'packId' => $pack->getId()])
    : $view['router']->generate('packs_create', ['packId' => $pack->getId()])); ?>">
    <a href="#add-entity" class="big-add" data-toggle="modal" data-target="#add-entity">Add
        <span>+</span> individual</a>
    <?php print $this->render('AdminBundle:Admin:cell-collection.html.php', [
        'tables' => ['ss_user' => ['first', 'last', 'email', 'id', 'deleted']],
        'entities' => $users->toArray(),
        'entityIds' => $ids,
        'removedEntities' => array_values($users->filter(function (User $user) use ($pack) {return !empty($up = $user->getUserPack($pack)) ? $up->getRemoved() : false;})->toArray())]); ?>
</form>
