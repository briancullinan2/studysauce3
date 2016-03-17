<?php
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

/** @var Pack $pack */
$groups = $pack->getGroups()->toArray();
$users = $pack->getUsers()->toArray();
$entityIds = [];

$diffUsers = array_filter($users, function (User $u) use (&$entityIds, $groups) {
    $entityIds[] = 'ss_user-' . $u->getId();
    return count(array_intersect($u->getGroups()->map(function (Group $g) {return $g->getId();})->toArray(), array_map(function (Group $g) {return $g->getId();}, $groups))) == 0;
});

?>

<div>
    <label class="input">
        <span>Groups</span><br />
        <input type="text" value="<?php print implode(', ', array_map(function (Group $g) {return $g->getName();}, $groups)); ?>"
            data-groups="<?php print $view->escape(json_encode(array_map(function (Group $u) use (&$entityIds) {
                $entityIds[] = 'ss_group-' . $u->getId();
                return [
                    'table' => 'ss_group',
                    'value' => $u->getId(),
                    'text' => $u->getName() . ' ' . $u->getDescription(),
                    0 => $u->getId()
                ];
            }, $groups))); ?>"
               data-users="<?php print $view->escape(json_encode(array_map(function (User $u) use (&$entityIds) {
                   return [
                       'table' => 'ss_user',
                       'value' => $u->getId(),
                       'text' => $u->getFirst() . ' ' . $u->getLast(),
                       0 => $u->getEmail()
                   ];
               }, $diffUsers))); ?>"
               data-entities="<?php print $view->escape(json_encode($entityIds)); ?>"/>
    </label>
    <a href="#users-groups" data-target="#users-groups" data-toggle="modal">+</a>
</div>