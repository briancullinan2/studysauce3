<?php
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

/** @var Pack $pack */
$groups = $pack->getGroups()->toArray();
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
<label class="input"><input type="text" name="title" value="<?php print $view->escape($pack->getTitle()); ?>" /></label>
<span class="count"><?php print array_sum($pack->getGroups()->map(function (Group $g) {return $g->getUsers()->count();})->toArray()) + count($diffUsers); ?></span>
