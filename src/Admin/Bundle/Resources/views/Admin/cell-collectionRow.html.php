<?php

//TODO: in javascript convert this to window.views.__vars? global $i;
use Admin\Bundle\Controller\AdminController;

AdminController::$radioCounter++;

$dataEntity = AdminController::toFirewalledEntityArray($entity, $tables);
$tableNames = array_keys($tables);

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
$newRow = jQuery($view['slots']->get('cell-collection-row'));
// update names of fields
$newRow->find('span')->text($view->escape(concat($dataEntity[$tables[$dataEntity['table']][0]], ' ', $dataEntity[$tables[$dataEntity['table']][1]])));
$newRow->find('input[type="checkbox"]')->attr('name', concat(implode('_', $tableNames) , '[' , AdminController::$radioCounter , '][id]'))->val($dataEntity['id']);
$newRow->find('input[type="hidden"]')->attr('name', concat(implode('_', $tableNames) , '[' , AdminController::$radioCounter , '][remove]'));

$context->append($newRow);
print ($context->html());