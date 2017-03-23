<?php

use Admin\Bundle\Controller\AdminController;
use StudySauce\Bundle\Entity\Pack;

$entityIds = [];
foreach($results['pack'] as $p) {
    /** @var Pack $p */
    $entityIds[count($entityIds)] = implode('', ['pack-' , $p->getId()]);
}

?>
<header class="<?php print ($table); ?>">
    <label>Study pack</label>
    <label>Members</label>
    <label>Cards</label>
    <a href="#create-entity" data-target="#create-entity" data-toggle="modal"
       name="ss_group[groupPacks]"
       data-tables="<?php print ($view->escape(json_encode(['pack' => AdminController::$defaultMiniTables['pack']]))); ?>"
       data-entities="<?php print ($view->escape(json_encode($entityIds))); ?>"
       data-action="<?php print ($view['router']->generate('save_group', [
           'ss_group' => ['id' => $request['ss_group-id']],
           'tables' => ['ss_group' => ['id', 'groupPacks']]
       ])); ?>" class="big-add">Add
        <span>+</span> new pack</a>
</header>
