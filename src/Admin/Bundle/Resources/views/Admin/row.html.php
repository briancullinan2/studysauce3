<?php
use Admin\Bundle\Controller\AdminController;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;

/** @var GlobalVariables $app */

/** @var User|Group $entity */
$context = !empty($context) ? $context : jQuery($this);

$rowId = implode('', [$table , '-id-']);
if(method_exists($entity, 'getId')) {
    $rowId = implode('', [$rowId , $entity->getId(), empty($entity->getId()) ? implode('', [' new-id-', AdminController::makeID()]) : '']);
}

if(isset($entity->newId)) {
    $row = $context->filter(implode('', ['.', $rowId, ',.new-id-', $entity->newId]));
}
else {
    $row = $context->filter(implode('', ['.', $rowId]));
}
$expandable = isset($request['expandable']) && is_array($request['expandable'])
    ? $request['expandable']
    : [];

$view['slots']->start('result-row');
?>
<div class="<?php print ($table); ?>-row results-<?php print ($tableId); ?> <?php
print ($rowId); ?> <?php
print (isset($request['edit']) && ($request['edit'] === true
    || is_array($request['edit']) && in_array($table, $request['edit']))
    ? 'edit'
    : (isset($request['read-only']) && ($request['read-only'] === false || $request['read-only'] === 'false'
        || is_array($request['read-only']) && !in_array($table, $request['read-only']))
        ? ''
        : 'read-only')); ?> <?php
print (isset($expandable[$table]) ? 'expandable' : ''); ?> <?php
print (!empty($classes) ? $classes : ''); ?> ">
    <?php print ($view->render('AdminBundle:Admin:cells.html.php', [
        'entity' => $entity,
        'tables' => $tables,
        'table' => $table,
        'request' => $request,
        'results' => $results,
        'tableId' => $tableId])); ?>
    <label class="checkbox"><input type="checkbox" name="selected"/><i></i></label>
</div>
<?php if (isset($expandable[$table])) { ?>
    <div class="expandable <?php
    print (!empty($classes) ? $classes : ''); ?>">
    <?php print ($view->render('AdminBundle:Admin:cells.html.php', [
        'entity' => $entity,
        'tables' => $expandable,
        'table' => $table,
        'request' => $request,
        'results' => $results,
        'tableId' => implode('', [$tableId , '-expandable'])])); ?>
    </div><?php
}
$view['slots']->stop();

$newRow = $view['slots']->get('result-row');
if($row->length == 0 || !$row->is('.edit')) {
    print ($newRow);
    return; // stop short up updates
}

// update existing rows
if(isset($entity->newId)) {
    // update newId?
    $row->removeClass(implode('', [$table , '-id-']))->removeClass(implode('', ['new-id-', $entity->newId]))->addClass($rowId);
}

// replace actions content even if the row already exists in edit mode, so they can create and delete without ever refreshing
$row->children('[class*="actions"]')->replaceWith(jQuery($newRow)->children('[class*="actions"]'));
