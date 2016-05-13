<?php
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;

/** @var GlobalVariables $app */

/** @var Card $card */

$rowId = $table . '-id-' . $card->getId();

$expandable = isset($searchRequest['expandable']) && is_array($searchRequest['expandable'])
    ? $searchRequest['expandable']
    : [];
?>
<div class="<?php print $table; ?>-row <?php print (empty($card->getResponseType()) || $card->getResponseType() == 'fc' ? '' : ('type-' . strtolower($card->getResponseType()))); ?> <?php
print $rowId; ?> <?php
print (isset($searchRequest['edit']) && ($searchRequest['edit'] === true || is_array($searchRequest['edit']) && in_array($table, $searchRequest['edit']))
    ? 'edit'
    : (isset($searchRequest['read-only']) && ($searchRequest['read-only'] === false || is_array($searchRequest['read-only']) && !in_array($table, $searchRequest['read-only']))
        ? ''
        : 'read-only')); ?> <?php
print (isset($expandable[$table]) ? 'expandable' : ''); ?> <?php
print (!empty($classes) ? $classes : ''); ?>">
    <?php print $view->render('AdminBundle:Admin:cells.html.php', ['entity' => $card, 'tables' => $tables, 'table' => $table, 'allGroups' => $allGroups, 'searchRequest' => $searchRequest, 'results' => $results]); ?>
    <label class="checkbox"><input type="checkbox" name="selected"/><i></i></label>
</div>
<?php if (isset($expandable[$table])) { ?>
    <div class="expandable <?php
    print (!empty($classes) ? $classes : ''); ?>">
    <?php print $view->render('AdminBundle:Admin:cells.html.php', ['entity' => $card, 'tables' => $expandable, 'table' => $table, 'allGroups' => $allGroups, 'searchRequest' => $searchRequest, 'results' => $results]); ?>
    </div><?php
}