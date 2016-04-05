<?php
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;

/** @var GlobalVariables $app */

/** @var User|Group $entity */

$rowId = $table . '-id-' . $entity->getId();

$expandable = $app->getRequest()->get('expandable');
if (!is_array($expandable)) {
    $expandable = [];
}
?>
<div class="<?php print $table; ?>-row <?php
print $rowId; ?> <?php
print ($app->getRequest()->get('edit') === true || is_array($app->getRequest()->get('edit')) && in_array($table, $app->getRequest()->get('edit'))
    ? 'edit'
    : 'read-only'); ?> <?php
print (isset($expandable[$table]) ? 'expandable' : ''); ?> <?php
print (!empty($classes) ? $classes : ''); ?>">
    <?php print $view->render('AdminBundle:Admin:cells.html.php', ['entity' => $entity, 'tables' => $tables, 'table' => $table, 'allGroups' => $allGroups]); ?>
    <label class="checkbox"><input type="checkbox" name="selected"/><i></i></label>
</div>
<?php if (isset($expandable[$table])) { ?>
    <div class="expandable">
    <?php print $view->render('AdminBundle:Admin:cells.html.php', ['entity' => $entity, 'tables' => $expandable, 'table' => $table, 'allGroups' => $allGroups]); ?>
    </div><?php
}