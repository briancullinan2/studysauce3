<?php

use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

global $pack;

if (!empty($pack)) {
    /** @var Pack[] $pack */
    /** @var Group[] $ss_group */
    $users = $pack[0]->getUsers()->toArray();
    $entityIds = [];
    $diffUsers = array_values(array_filter($users, function (User $u) use (&$entityIds, $ss_group) {
        $entityIds[] = 'ss_user-' . $u->getId();
        return count(array_intersect($u->getGroups()->map(function (Group $g) {
            return $g->getId();
        })->toArray(), array_map(function (Group $g) {
            return $g->getId();
        }, $ss_group))) == 0;
    }));

    foreach ($ss_group as $g) {
        $entityIds[] = 'ss_group-' . $g->getId();
    }


    if (empty($ss_group)) { ?> <div class="empty-packs">No subgroups</div> <?php }
}

?>
<div class="highlighted-link form-actions <?php print $table; ?>">
    <a href="<?php print $view['router']->generate('groups_new'); ?>" class="big-add">Add
        <span>+</span> new subgroup</a><br/>
    <?php
    if (!empty($entityIds)) {
        /** @var Group[] $ss_group */
        print $this->render('AdminBundle:Admin:cell-collection.html.php', [
            'tables' => [
                'ss_user' => ['first', 'last', 'email', 'id', 'deleted'],
                'ss_group' => ['name', 'userCountStr', 'descriptionStr', 'id', 'deleted']],
            'entityIds' => $entityIds]);
        ?>
        <a href="#add-entity" title="Manage users and groups" data-target="#add-entity" data-toggle="modal" class="big-add"><span>+</span>&nbsp;</a>
    <?php } ?>
</div>

