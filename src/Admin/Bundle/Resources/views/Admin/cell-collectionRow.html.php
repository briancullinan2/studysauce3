<?php

//TODO: in javascript convert this to window.views.__vars? global $i;
use Admin\Bundle\Controller\AdminController;

AdminController::$radioCounter++;

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
$newRow->find('span')->text(concat($entity[$tables[$entity['table']][0]], ' ', $entity[$tables[$entity['table']][1]]));
$newRow->find('input[type="checkbox"]')->attr('name', concat(implode('_', $tableNames) , '[' , AdminController::$radioCounter , '][id]'))->val($entity['id']);
$newRow->find('input[type="hidden"]')->attr('name', concat(implode('_', $tableNames) , '[' , AdminController::$radioCounter , '][remove]'));
if(isset($entity['removed']) && $entity['removed']) {
    $newRow->find('[href="#subtract-entity"]')->remove();
}
else {
    $newRow->find('[href="#insert-entity"]')->remove();
}
$context->append($newRow);
print ($context->html());