<?php
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

/** @var Pack $pack */
$groups = $pack->getGroups()->filter(function (Group $g) {return !$g->getDeleted();})->toArray();
$users = $pack->getUsers()->toArray();
$entityIds = [];
$groupIds = [];
$diffIds = [];
$diffUsers = array_values(array_filter($users, function (User $u) use (&$entityIds, $groups) {
    $entityIds[] = 'ss_user-' . $u->getId();
    return count(array_intersect($u->getGroups()->map(function (Group $g) {
        return $g->getId();
    })->toArray(), array_map(function (Group $g) {
        return $g->getId();
    }, $groups))) == 0;
}));

?>

<div class="entity-search">
    <label class="input">
        <input type="text"
               data-tables="<?php print $view->escape(json_encode(['ss_user' => ['first', 'last', 'email', 'id', 'deleted'], 'ss_group' => ['name', 'userCountStr', 'description', 'id', 'deleted']])); ?>"
               data-ss_group="<?php print $view->escape(json_encode(array_map(function (Group $u) use (&$entityIds, &$groupIds) {
                   $entityIds[] = 'ss_group-' . $u->getId();
                   $groupIds[] = 'ss_group-' . $u->getId();
                   return [
                       'table' => 'ss_group',
                       'value' => 'ss_group-' . $u->getId(),
                       'text' => $u->getName() . ' ' . $u->getUserCountStr(),
                       0 => $u->getDescription()
                   ];
               }, $groups))); ?>"
               data-ss_user="<?php print $view->escape(json_encode(array_map(function (User $u) use (&$entityIds, &$diffIds) {
                   $diffIds[] = 'ss_user-' . $u->getId();
                   return [
                       'table' => 'ss_user',
                       'value' => 'ss_user-' . $u->getId(),
                       'text' => $u->getFirst() . ' ' . $u->getLast(),
                       0 => $u->getEmail()
                   ];
               }, $diffUsers))); ?>"
               data-entities="<?php print $view->escape(json_encode($entityIds)); ?>"
               value="<?php print implode(' ', array_merge($groupIds, $diffIds)); ?>" />
    </label>
    <a href="#add-entity" data-target="#add-entity" data-toggle="modal">+</a>
</div>