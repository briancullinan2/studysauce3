<?php

use Admin\Bundle\Controller\AdminController;
use StudySauce\Bundle\Entity\Pack;

$entityIds = [];
foreach($results['pack'] as $p) {
    /** @var Pack $p */
    $entityIds[count($entityIds)] = 'pack-' . $p->getId();
}

?>
<header class="pack">
    <label>Study pack</label>
    <label>Members</label>
    <label>Cards</label>
    <a href="#create-entity" data-target="#create-entity" data-toggle="modal"
       name="ss_group[groupPacks]"
       data-tables="<?php print ($view->escape(json_encode(['pack' => AdminController::$defaultMiniTables['pack']]))); ?>"
       data-entities="<?php print ($view->escape(json_encode($entityIds))); ?>"
       data-action="<?php print ($view['router']->generate('save_group', [
           'ss_group' => ['id' => $searchRequest['ss_group-id']],
           'tables' => ['ss_group' => ['groupPacks']]
       ])); ?>" class="big-add">Add
        <span>+</span> new pack</a>
</header>
