<?php

use Admin\Bundle\Controller\AdminController;
use DateTime as Date;

$tableNames = array_keys($tables);

$view['slots']->start('cell-collection-create'); ?>
    <div class="entity-search <?php print (implode(' ', $tableNames)); ?>">
        <label class="input">
            <input type="text" name="<?php print (implode('_', $tableNames)); ?>" value=""
                   data-confirm="<?php print (!isset($dataConfirm) || $dataConfirm ? 'true' : 'false'); ?>" /></label>
    </div>
<?php $view['slots']->stop();


$view['slots']->start('cell-collection-header'); ?>
    <header>
        <label></label>
        <a href="#add-entity" class="big-add" data-toggle="modal" data-target="#add-entity">Add
            <span>+</span> individual</a>
    </header>
<?php $view['slots']->stop();



$context = !empty($context) ? $context : jQuery($this);
$search = $context->find('.entity-search');
if($search->length == 0) {
    $search = $context->append($view['slots']->get('cell-collection-create'))->find('.entity-search');
    $input = $search->find('.input > input');
}
else {
    $input = $search->find('.input > input');
}

$entityIds = isset($entityIds) && is_array($entityIds) ? $entityIds : [];
$listIds = [];
$dataTypes = (array)(new stdClass());
$removedEntities = isset($removedEntities) && is_array($removedEntities) ? $removedEntities : [];
if (isset($entities)) {
    foreach ($entities as $entity) {
        $dataEntity = AdminController::toFirewalledEntityArray($entity, $tables);
        if(in_array($entity, $removedEntities)) {
            $dataEntity['removed'] = true;
        }
        if(!isset($dataEntity['removed'])) {
            $dataEntity['removed'] = false;
        }
        $key = concat($dataEntity['table'] , '-' , $dataEntity['id']);
        $table = $dataEntity['table'];
        $listIds[count($listIds)] = $key;
        $unsetId = array_search($key, $entityIds);
        if (!$dataEntity['removed'] && $unsetId === false) {
            $entityIds[count($entityIds)] = $key;
        }
        else if ($dataEntity['removed'] && $unsetId !== false) {
            array_splice($entityIds, $unsetId, 1);
        }
        if(!isset($dataTypes[$table])) {
            // TODO: fix this syntax in JS
            $dataTypes[$table] = [];
        }
        $dataTypes[$table][count($dataTypes[$table])] = $dataEntity;

        // if we are dealing with a list of entities

        if (isset($entities) && (!isset($inline) || $inline !== true)) {
            $newRow = jQuery($view->render('AdminBundle:Admin:cell-collectionRow.html.php', ['entity' => $dataEntity, 'tables' => $tables]));
            $newRow->find('input[name*="[remove]"]')->val($dataEntity['removed'] ? 'true' : 'false');

            $existing = $search->find('.input ~ .checkbox')->find(concat('input[name^="' , $table , '["][value="' , $dataEntity['id'] , '"]'));
            if($existing->length == 0) {
                // TODO: insert in the right place
                if($search->find('header:contains(Removed)')->length == 0) {

                }
                $search->append($newRow);
            }
            else {
                $existing->parents('.checkbox')->replaceWith($newRow);
            }
        }
    }
}

if (isset($entities) && (!isset($inline) || $inline !== true)) {
    $header = $search->find('.input + header');
    if($header->length == 0) {
        $header = jQuery($view['slots']->get('cell-collection-header'))->insertAfter($search->find('.input'));
    }
    $headerTitle = '';
    $placeHolder = '';
    foreach ($tableNames as $t) {
        $headerTitle = concat((!empty($headerTitle) ? '/' : ''), (isset($dataTypes[$t])
            ? count($dataTypes[$t])
            : 0), ' ', str_replace('ss_', '', $t), 's');

        $placeHolder = concat((!empty($placeHolder) ? '/' : ''), ucfirst(str_replace('ss_', '', $t)));
    }
    $headerTitle = concat('Members (', $headerTitle, ')');
    $placeHolder = concat('Search for ', $placeHolder);
    $header->find('label')->text($headerTitle);

    // some final tweak to the input field
    $input->attr('placeholder', $placeHolder);
}

// this is the update stuff that we do every time the template is called

$entityIds = array_values($entityIds);
$input->val(isset($inline) && $inline === true ? implode(' ', $listIds) : '');
// force it to use string keys
$input->data('tables', $tables)
    ->data('oldValue', '')
    ->data('entities', $entityIds)
    ->attr('data-tables', json_encode($tables))
    ->attr('data-entities', json_encode($entityIds));
foreach ($tableNames as $t) {
    $types = isset($dataTypes[$t]) ? $dataTypes[$t] : [];
    $input->data($t, $types)
        ->attr(concat('data-' , $t), json_encode($types));
}

/*
 * TODO: merge this with template scripts
function updateRows (toField, value, item) {
    isSettingSelectize = true;
    var tableName = value.split('-')[0];
    var existing = (toField.data('entities') || []);
    var existingEntities = toField.data(tableName) || [];
    var oldValue = toField.val().split(' ');
    var valueField = toField[0].selectize.settings.valueField;
        var entity;
        if((entity = existingEntities.filter(function (e) {return e[valueField] == item[valueField]})).length > 0) {
            entity[0].remove = item.remove;
        }
        else {
            existingEntities[existingEntities.length] = item;
        }
        if (item.remove) {
            existing = existing.filter(function (i) {return i != item[valueField]});
            oldValue = oldValue.filter(function (i) {return i != item[valueField]});
        }
        else {
            if(existing.indexOf(item[valueField]) == -1) {
                existing[existing.length] = item[valueField];
            }
            if(oldValue.indexOf(item[valueField]) == -1) {
                oldValue[oldValue.length] = item[valueField];
            }
        }
        toField.data('oldValue', oldValue.join(' '));
        toField.data('entities', existing);
        toField.data(tableName, existingEntities);
        isSettingSelectize = false;
    }
*/

print ($context->html());


