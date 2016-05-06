<?php

use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

if (!empty($results['pack'])) {
    $users = $results['pack'][0]->getUsers()->toArray();
    $entityIds = [];
    $diffUsers = array_values(array_filter($users, function (User $u) use (&$entityIds, $results) {
        $entityIds[] = 'ss_user-' . $u->getId();
        return count(array_intersect($u->getGroups()->map(function (Group $g) {
            return $g->getId();
        })->toArray(), array_map(function (Group $g) {
            return $g->getId();
        }, $results['ss_group']))) == 0;
    }));

    foreach ($results['ss_group'] as $g) {
        $entityIds[] = 'ss_group-' . $g->getId();
    }
}
?>
<div class="highlighted-link form-actions <?php print $table; ?>">
    <?php if (empty($results['ss_group'])) { ?> <div class="empty-packs">No subgroups</div> <?php } ?>
</div>

