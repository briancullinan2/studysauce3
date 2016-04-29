<header class="ss_group">
    <label>Subgroups</label>
    <label>Members</label>
    <label>Packs</label>
</header>
<?php
global $subGroupParent;
if(!empty($subGroupParent)) {
    print $view->render('AdminBundle:Admin:row.html.php', ['tables' => $tables, 'allGroups' => $allGroups, 'searchRequest' => $searchRequest, 'entity' => $subGroupParent[0], 'table' => 'ss_group']);
}

global $pack;
if(!empty($pack)) {
    print $view->render('AdminBundle:Admin:row.html.php', ['tables' => $tables, 'allGroups' => $allGroups, 'searchRequest' => $searchRequest, 'entity' => $pack[0], 'table' => 'pack']);
}