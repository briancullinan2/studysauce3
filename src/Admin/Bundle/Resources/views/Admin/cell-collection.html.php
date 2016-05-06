<?php

use Admin\Bundle\Controller\AdminController;
use DateTime as Date;

$view['slots']->start('cell-collection-create'); ?>
    <div class="entity-search <?php print (implode(' ', array_keys($tables))); ?>">
        <label class="input">
            <input type="text" name="<?php print (implode('_', array_keys($tables))); ?>" value=""
                   placeholder="Search for <?php
                   $name = '';
                   foreach(array_keys($tables) as $t) {
                       $name = concat((!empty($name) ? '/' : '') , ucfirst(str_replace('ss_', '', $t)));
                   }
                   print ($name); ?>"
                   data-tables="<?php print ($view->escape(json_encode($tables))); ?>"
                   data-confirm="<?php print (!isset($dataConfirm) || $dataConfirm ? 'true' : 'false'); ?>" /></label>
    </div>
<?php $view['slots']->stop();


$view['slots']->start('cell-collection-header-removed'); ?>
    <header>
        <label></label>
        <a href="#add-entity" class="big-add" data-toggle="modal" data-target="#add-entity">Add
            <span>+</span> individual</a>
    </header>
<?php $view['slots']->stop();


$view['slots']->start('cell-collection-row'); ?>
    <label class="checkbox buttons-1">
        <input type="checkbox" name="" value="" checked="checked" />
        <input type="hidden" name="" />
        <i></i>
        <span class="entity-title"></span>
        <a href="#insert-entity" title="Add">&nbsp;</a>
        <a href="#subtract-entity" title="Remove"></a>
    </label>
<?php $view['slots']->stop();


$context = jQuery($this);
$search = $context->find('.entity-search');
if($search->length == 0) {
    $search = $context->append($view['slots']->get('cell-collection-create'))->find('.entity-search');
    if (isset($entities) && (!isset($inline) || $inline !== true)) {
        $headerTitle = '';
        foreach (array_keys($tables) as $t) {
            $headerTitle = concat((!empty($headerTitle) ? '/' : ''), (isset($dataTypes[$t])
                ? count($dataTypes[$t])
                : 0), ' ', str_replace('ss_', '', $t), 's');
        }
        $headerTitle = concat('Members (', $headerTitle, ')');
        $search->find('.input + header')->find('label')->text($headerTitle);
    }
}
$input = $search->find('input');

$i = 0;
$entityIds = isset($entityIds) && is_array($entityIds) ? $entityIds : [];
$listIds = [];
$dataTypes = [];
$removedEntities = isset($removedEntities) && is_array($removedEntities) ? $removedEntities : [];
if (isset($entities)) {
    foreach ($entities as $u) {
        if((!is_array($u) || empty($joinTable = $u['table']))
            && empty($joinTable = AdminController::getJoinTable($u))) {
            continue;
        }

        $remove = in_array($u, $removedEntities);
        $key = concat($joinTable , '-' , $u->getId());
        $listIds[count($listIds)] = $key;
        $unsetId = array_search($key, $entityIds);
        if (!$remove && $unsetId === false) {
            $entityIds[count($entityIds)] = $key;
        }
        else if ($remove && $unsetId !== false) {
            unset($entityIds[$unsetId]);
        }

        $first = call_user_func_array([$u, 'get' . ucfirst($tables[$joinTable][0])], []);
        $second = call_user_func_array([$u, 'get' . ucfirst($tables[$joinTable][1])], []);
        $third = call_user_func_array([$u, 'get' . ucfirst($tables[$joinTable][2])], []);
        if($first instanceof Date) {
            $first = $first->format('r');
        }
        if($second instanceof Date) {
            $second = $second->format('r');
        }
        if($third instanceof Date) {
            $third = $third->format('r');
        }
        if(!isset($dataTypes[$joinTable])) {
            $dataTypes[$joinTable] = [];
        }
        $dataTypes[$joinTable][count($dataTypes[$joinTable])] = [
            'table' => $joinTable,
            'remove' => $remove,
            'value' => $key,
            'text' => concat($first , ' ' , $second),
            0 => $third
        ];

        // if we are dealing with a list of entities

        if (isset($entities) && (!isset($inline) || $inline !== true)) {
            $existing = $search->find('.input ~ .checkbox')->find(concat('input[name^="' , $joinTable , '["][value="' , $u->getId() , '"]'));
            if($existing->length == 0) {
                $newRow = jQuery($view['slots']->get('cell-collection-create'))->insertAfter($input);
                // update names of fields
                $newRow->find('span')->text($view->escape(concat(call_user_func_array([$u, concat('get', ucfirst($tables[$joinTable][0]))], []), ' ', call_user_func_array([$u, concat('get', ucfirst($tables[$joinTable][1]))], []))));
                $newRow->find('input[type="checkbox"]')->attr('name', concat(implode('_', array_keys($tables)) , '[' , $i , '][id]'))->val($u->getId());
                $newRow->find('input[type="hidden"]')->attr('name', concat(implode('_', array_keys($tables)) , '[' , $i , '][remove]'));
                if($remove) {
                    $newRow->find('[href="#subtract-entity"]')->remove();
                }
                else {
                    $newRow->find('[href="#insert-entity"]')->remove();
                }
            }
            $existing->find('[name*="[remove]"]')->val($remove ? 'true' : 'false');
            // TODO: insert in the right place
            if($search->find('header:contains(Removed)')->length == 0) {

            }

            $i++;
        }

    }
}

$entityIds = array_values($entityIds);
$input->val(isset($inline) && $inline === true ? implode(' ', $listIds) : '');
$input->attr('data-entities', json_encode($entityIds));
foreach (array_keys($tables) as $table) {
    $input->attr(concat('data-' , $table), json_encode(isset($dataTypes[$table]) ? $dataTypes[$table] : []));
}


print ($context->html());


