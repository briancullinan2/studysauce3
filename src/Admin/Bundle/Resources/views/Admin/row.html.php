<?php
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;

/** @var GlobalVariables $app */

/** @var User|Group $entity */

$rowId = $table . '-id-';
if(method_exists($entity, 'getId')) {
    $rowId .= $entity->getId();
}

$expandable = isset($searchRequest['expandable']) && is_array($searchRequest['expandable'])
    ? $searchRequest['expandable']
    : [];
?>
<div class="<?php print $table; ?>-row <?php
print $rowId; ?> <?php
print (isset($searchRequest['edit']) && ($searchRequest['edit'] === true || is_array($searchRequest['edit']) && in_array($table, $searchRequest['edit']))
    ? 'edit'
    : (isset($searchRequest['read-only']) && ($searchRequest['read-only'] === false || is_array($searchRequest['read-only']) && !in_array($table, $searchRequest['read-only']))
        ? ''
        : 'read-only')); ?> <?php
print (isset($expandable[$table]) ? 'expandable' : ''); ?> <?php
print (!empty($classes) ? $classes : ''); ?>">
    <?php print $view->render('AdminBundle:Admin:cells.html.php', ['entity' => $entity, 'tables' => $tables, 'table' => $table, 'allGroups' => $allGroups, 'searchRequest' => $searchRequest]); ?>
    <label class="checkbox"><input type="checkbox" name="selected"/><i></i></label>
</div>
<?php if (isset($expandable[$table])) { ?>
    <div class="expandable <?php
    print (!empty($classes) ? $classes : ''); ?>">
    <?php print $view->render('AdminBundle:Admin:cells.html.php', ['entity' => $entity, 'tables' => $expandable, 'table' => $table, 'allGroups' => $allGroups, 'searchRequest' => $searchRequest]); ?>
    </div><?php
}