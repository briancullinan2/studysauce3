<?php

$getJoinTable = function ($u) {
    $type = get_class($u);
    $ti = array_search($type, \Admin\Bundle\Controller\AdminController::$allTableClasses);
    if($ti === false) {
        $type = get_parent_class($u);
        $ti = array_search($type, \Admin\Bundle\Controller\AdminController::$allTableClasses);
    }
    if($ti === false) {
        return false;
    }
    $joinTable = \Admin\Bundle\Controller\AdminController::$allTableMetadata[$ti]->table['name'];
    return $joinTable;
};

$entityIds = isset($entityIds) && is_array($entityIds) ? $entityIds : [];
$listIds = [];
$dataTypes = [];
$removedEntities = isset($removedEntities) && is_array($removedEntities) ? $removedEntities : [];
if (isset($entities)) {
    foreach ($entities as $u) {
        if(empty($joinTable = $getJoinTable($u))) {
            continue;
        }
        $key = $joinTable . '-' . $u->getId();
        $listIds[] = $key;
        $remove = in_array($u, $removedEntities);
        $unsetId = array_search($key, $entityIds);
        if (!$remove && $unsetId === false) {
            $entityIds[] = $key;
        }
        else if ($remove && $unsetId !== false) {
            unset($entityIds[$unsetId]);
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
            'remove' => $remove,
            'value' => $key,
            'text' => $first . ' ' . $second,
            0 => $third
        ];
    }
}
// template expects an array down below
$entityIds = array_values($entityIds);
?>

<div class="entity-search <?php print implode(' ', array_keys($dataTypes)); ?>">
    <label class="input">
        <input type="text" name="<?php print implode('_', array_keys($tables)); ?>" value="<?php print (isset($inline) && $inline === true ? implode(' ', $listIds) : ''); ?>"
               placeholder="Search for <?php print implode('/', array_map(function ($t) {
                   return ucfirst(str_replace('ss_', '', $t));
               }, array_keys($tables))); ?>"
            <?php
            foreach (array_keys($tables) as $table) {
                print ' data-' . $table . '="' . $view->escape(json_encode(isset($dataTypes[$table]) ? $dataTypes[$table] : [])) . '"';
            } ?>
                data-entities="<?php print $view->escape(json_encode($entityIds)); ?>"
                data-tables="<?php print $view->escape(json_encode($tables)); ?>"
                data-confirm="<?php print (!isset($dataConfirm) || $dataConfirm ? 'true' : 'false'); ?>" /></label>
    <?php
    if (isset($entities) && (!isset($inline) || $inline !== true)) {
        $i = 0;
        ?>
        <header><label>Current <?php print implode('/', array_map(function ($t) {
                    return ucfirst(str_replace('ss_', '', $t)) . 's';
                }, array_keys($tables))); ?></label></header>
        <?php
        foreach ($entities as $u) {
            if(in_array($u, $removedEntities)) {
                continue;
            }
            if(empty($joinTable = $getJoinTable($u))) {
                continue;
            }
            ?>
            <label class="checkbox buttons-1">
                <input type="checkbox" name="<?php print implode('_', array_keys($tables)); ?>[<?php print $i; ?>][id]"
                       value="<?php print $u->getId(); ?>"
                       checked="checked"/>
                <i></i>
                <span><?php print $view->escape($u->{'get' . ucfirst($tables[$joinTable][0])}()) . ' ' . $view->escape($u->{'get' . ucfirst($tables[$joinTable][1])}()); ?></span>
                <a href="#subtract-entity" title="Remove"></a>
            </label>
            <?php $i++;
        }

        if (!empty($removedEntities)) {
            ?>
            <header><label>Removed <?php print implode('/', array_map(function ($t) {
                        return ucfirst(str_replace('ss_', '', $t)) . 's';
                    }, array_keys($tables))); ?></label></header>
            <?php
            foreach ($removedEntities as $u) {
                if(empty($joinTable = $getJoinTable($u))) {
                    continue;
                }
                ?>
                <label class="checkbox buttons-1">
                    <input type="checkbox" name="<?php print implode('_', array_keys($tables)); ?>[<?php print $i; ?>][id]"
                           value="<?php print $u->getId(); ?>" checked="checked"/>
                    <input type="hidden" name="<?php print implode('_', array_keys($tables)); ?>[<?php print $i; ?>][remove]" />

                    <i></i>
                    <span><?php print $view->escape($u->{'get' . ucfirst($tables[$joinTable][0])}()) . ' ' . $view->escape($u->{'get' . ucfirst($tables[$joinTable][1])}()); ?></span>
                    <a href="#insert-entity" title="Add">&nbsp;</a>
                </label>
                <?php $i++;
            }
        }
        ?>

        <label class="checkbox template changed">
            <input type="checkbox" name="<?php print implode('_', array_keys($tables)); ?>[{i}][id]" value="{value}"
                   checked="checked"/>
            <i></i>
            <span>{<?php print implode('_', array_keys($tables)); ?>}</span>
            <a href="#insert-entity" title="Add">&nbsp;</a><a href="#subtract-entity" title="Remove">&nbsp;</a>
        </label>

    <?php } ?>
</div>