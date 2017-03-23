<?php
use StudySauce\Bundle\Entity\Group;

$subGroups = [];
$countUsers = 0;
$countPacks = 0;
$added = true;
while($added) {
    $added = false;
    foreach($results['allGroups'] as $g) {
        /** @var Group $g */
        if(!empty($g->getParent()) && isset($request['ss_group-id'])
            && ($g->getParent()->getId() == $request['ss_group-id'] || in_array($g->getParent()->getId(), $subGroups))
            && !in_array($g->getId(), $subGroups)) {
            $subGroups[count($subGroups)] = $g->getId();
            $countUsers += count($g->getUsers()->toArray());
            $countPacks += count($g->getPacks()->toArray());
            $added = true;
        }
    }
}

if (empty($results['ss_group-1'])) { ?>
    <footer class="ss_group">
        <label>Total in this Group</label>
        <label>0</label>
        <label>0</label>
    </footer>
<?php } else { ?>
    <footer class="ss_group">
        <label>Total in this Group</label>
        <label><?php print ($countUsers); ?></label>
        <label><?php print ($countPacks); ?></label>
    </footer>
<?php
}
