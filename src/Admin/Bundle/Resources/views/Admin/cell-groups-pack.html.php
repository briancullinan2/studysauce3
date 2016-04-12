<?php
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

/** @var Pack $pack */
/** @var Group[] $groups */
$groups = $pack->getGroups()->filter(function (Group $g) {return !$g->getDeleted();})->toArray();
$users = $pack->getUsers()->toArray();
$entityIds = [];
$diffUsers = array_values(array_filter($users, function (User $u) use (&$entityIds, $groups) {
    $entityIds[] = 'ss_user-' . $u->getId();
    return count(array_intersect($u->getGroups()->map(function (Group $g) {
        return $g->getId();
    })->toArray(), array_map(function (Group $g) {
        return $g->getId();
    }, $groups))) == 0;
}));

foreach($groups as $g) {
    $entityIds[] = 'ss_group-' . $g->getId();
}

print $view->render('AdminBundle:Admin:cell-collection.html.php', [
    'tables' => [
        'ss_user' => ['first', 'last', 'email', 'id', 'deleted'],
        'ss_group' => ['name', 'userCountStr', 'description', 'id', 'deleted']],
    'entities' => array_merge($diffUsers, $groups),
    'entityIds' => $entityIds,
    'inline' => true]);
?>
<a href="#add-entity" data-target="#add-entity" data-toggle="modal">+</a>
