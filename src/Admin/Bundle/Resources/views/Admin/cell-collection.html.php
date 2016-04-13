<?php
$entityIds = isset($entityIds) && is_array($entityIds) ? $entityIds : [];
$listIds = [];
$dataTypes = [];
if (isset($entities)) {
    foreach ($entities as $u) {
        $type = get_class($u);
        $ti = array_search($type, \Admin\Bundle\Controller\AdminController::$allTableClasses);
        if($ti === false) {
            $type = get_parent_class($u);
            $ti = array_search($type, \Admin\Bundle\Controller\AdminController::$allTableClasses);
        }
        if($ti === false) {
            continue;
        }
        $joinTable = \Admin\Bundle\Controller\AdminController::$allTableMetadata[$ti]->table['name'];
        $key = $joinTable . '-' . $u->getId();
        $listIds[] = $key;
        if (!in_array($key, $entityIds)) {
            $entityIds[] = $key;
        }
        $first = $u->{'get' . ucfirst($tables[$joinTable][0])}();
        $second = $u->{'get' . ucfirst($tables[$joinTable][1])}();
        $third = $u->{'get' . ucfirst($tables[$joinTable][2])}();
        if($first instanceof \DateTime) {
            $first = $first->format('r');
        }
        if($second instanceof \DateTime) {
            $second = $second->format('r');
        }
        if($third instanceof \DateTime) {
            $third = $third->format('r');
        }
        $dataTypes[$joinTable][] = [
            'table' => $joinTable,
            'value' => $key,
            'text' => $first . ' ' . $second,
            0 => $third
        ];
    }
}
?>

<div class="entity-search <?php print implode(' ', array_keys($dataTypes)); ?>">
    <label class="input">
        <input type="text" name="<?php print implode('_', array_keys($tables)); ?>" value="<?php print (isset($inline) && $inline === true ? implode(' ', $listIds) : ''); ?>"
               placeholder="Search for <?php print implode('/', array_map(function ($t) {
                   return ucfirst(str_replace('ss_', '', $t));
               }, array_keys($tables))); ?>"
            <?php
            foreach ($dataTypes as $t => $options) {
                print ' data-' . $t . '="' . $view->escape(json_encode($options)) . '"';
            } ?>
                data-entities="<?php print $view->escape(json_encode($entityIds)); ?>"
                data-tables="<?php print $view->escape(json_encode($tables)); ?>"/></label>
    <?php
    if (isset($entities) && (!isset($inline) || $inline !== true)) {
        $i = 0;
        foreach ($entities as $u) {
            $type = get_class($u);
            $ti = array_search($type, \Admin\Bundle\Controller\AdminController::$allTableClasses);
            $joinTable = \Admin\Bundle\Controller\AdminController::$allTableMetadata[$ti]->table['name'];
            ?>
            <label class="checkbox buttons-1">
                <input type="checkbox" name="<?php print implode('_', array_keys($tables)); ?>[<?php print $i; ?>][id]"
                       value="<?php print $u->getId(); ?>"
                       checked="checked"/>
                <i></i>
                <span><?php print $u->{'get' . ucfirst($tables[$joinTable][0])}() . ' ' . $u->{'get' . ucfirst($tables[$joinTable][1])}(); ?></span>
                <a href="#subtract-entity" title="Remove"></a>
            </label>
            <?php $i++;
        } ?>

        <label class="checkbox template">
            <input type="checkbox" name="<?php print implode('_', array_keys($tables)); ?>[{i}][id]" value="{value}"
                   checked="checked"/>
            <i></i>
            <span>{<?php print implode('_', array_keys($tables)); ?>}</span>
            <a href="#insert-entity" title="Add">&nbsp;</a><a href="#subtract-entity" title="Remove">&nbsp;</a>
        </label>

    <?php } ?>
</div>