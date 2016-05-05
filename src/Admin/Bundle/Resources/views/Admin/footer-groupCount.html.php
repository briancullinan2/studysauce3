<?php
if (empty($results['ss_group-1'])) { ?>
    <footer class="ss_group">
        <label>Total in this Group</label>
        <label>0</label>
        <label>0</label>
    </footer>
<?php } else {
    list($users, $packs, $groups) = $results['ss_group-1'][0]->getUsersPacksGroupsRecursively();
    ?>
    <footer class="ss_group">
        <label>Total in thisGroup</label>
        <label><?php print count($users); ?></label>
        <label><?php print count($packs); ?></label>
    </footer>
    <?php
}
