<?php
if (empty($results['ss_group-1'])) { ?>
    <footer class="ss_group">
        <label>Total in this Group</label>
        <label>0</label>
        <label>0</label>
    </footer>
<?php } else {
    $userPacksGroups = $results['ss_group-1'][0]->getUsersPacksGroupsRecursively();
    $users = $userPacksGroups[0];
    $packs = $userPacksGroups[1];
    $groups = $userPacksGroups[2];
    ?>
    <footer class="ss_group">
        <label>Total in this Group</label>
        <label><?php print (count($users)); ?></label>
        <label><?php print (count($packs)); ?></label>
    </footer>
    <?php
}
