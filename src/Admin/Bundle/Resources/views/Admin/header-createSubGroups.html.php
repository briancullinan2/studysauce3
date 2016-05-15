<?php
use Admin\Bundle\Controller\AdminController;
use StudySauce\Bundle\Entity\Group;

$entityIds = [];
foreach($results['ss_group'] as $p) {
    /** @var Group $p */
    $entityIds[count($entityIds)] = implode('', ['ss_group-' , $p->getId()]);
}

?>
<header class="<?php print ($table); ?>">
    <label>Subgroups</label>
    <label>Members</label>
    <label>Packs</label>
    <a href="#create-entity" data-target="#create-entity" data-toggle="modal"
       name="pack[groups]"
       data-tables="<?php print ($view->escape(json_encode(['ss_group' => AdminController::$defaultMiniTables['ss_group']]))); ?>"
       data-entities="<?php print ($view->escape(json_encode($entityIds))); ?>"
       data-action="<?php print ($view['router']->generate('save_group', [
           'pack' => ['id' => $request['pack-id']],
           'tables' => ['pack' => ['groups']]
       ])); ?>" class="big-add">Add
        <span>+</span> new subgroup</a>
</header>
