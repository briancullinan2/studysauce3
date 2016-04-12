<?php
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

/** @var Pack $pack */

$request = $app->getRequest();
$entityIds = [];
$users = $pack->getUsers();
$ids = $users->map(function (User $u) {return 'ss_user-' . $u->getId();})->toArray();
if(!empty($group = $request->get('ss_group-id'))) {
    $users = $users->filter(function (User $u) use ($group) {
        return $u->getGroups()->filter(function (Group $g) use ($group) {return $g->getId() == $group;})->count() > 0;});
}
?>
<label><?php print $users->count(); ?> users</label>
<?php print $this->render('AdminBundle:Admin:cell-collection.html.php', ['tables' => ['ss_user' => ['first', 'last', 'email', 'id', 'deleted']], 'entities' => $users->toArray(), 'entityIds' => $ids]); ?>

<a href="#add-entity" class="big-add" data-toggle="modal" data-target="#add-entity">Add
    <span>+</span> individual</a>
