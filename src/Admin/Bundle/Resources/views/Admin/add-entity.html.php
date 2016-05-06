<?php

$view->extend('AdminBundle:Admin:dialog.html.php');

$context = jQuery($this);
$dialog = $context->find('#add-entity');
$dialog->find('.tab-pane.active, li.active')->removeClass('active');
$dialog->find('.tab-pane,li')->hide();

$view['slots']->start('modal-header'); ?>
<h3>Add or Remove </h3>
<ul class="nav nav-tabs">
    <?php
    $first = true;
    foreach($tables as $tableName => $fields) {
        $tabItem = $dialog->find(concat('li a[href="#add-entity-' , $tableName , '"]'));
        if ($tabItem->length == 0) { ?>
            <li>
                <a href="#add-entity-<?php print ($tableName); ?>"
                   data-target="#add-entity-<?php print ($tableName); ?>"
                   data-toggle="tab"><?php print (ucfirst(str_replace('ss_', '', ($tableName)))); ?></a></li>
            <?php
        }
        else {
            $tabItem->parent()->show();
        }

        if ($first) {
            $tabItem->parent()->addClass('active');
        }
    }
        ?></ul>
<?php $view['slots']->stop();

$view['slots']->start('modal-body'); ?>
<form action="<?php print ($view['router']->generate('command_callback')); ?>" method="post">
    <div class="tab-content">
        <?php
        $first = true;
        foreach($tables as $tableName => $fields) {
            $entityField = $dialog->find(concat('input[name="', $tableName, '"][type="text"]'));
            if ($entityField->length == 0) { ?>
                <div id="add-entity-<?php print ($tableName); ?>" class="tab-pane">
                    <?php ($this->render('AdminBundle:Admin:cell-collection.html.php', ['tables' => $tables[$tableName], 'entities' => $entities])); ?>
                </div>
            <?php }
            else {
                $entityField->parents('.tab-pane')->show();
                $tmpTables = [];
                $tmpTables[$tableName] = $tables[$tableName];
                // TODO: move this to cell-collection
                $entityField->data('tables', $tmpTables);
                $entityField->data('oldValue', '');
                $entityField->data($tableName, $entities);

                // remove existing rows
                $dialog->find('.checkbox:not(.template)')->remove();

                if($entityField->is('.selectized')) {
                    $entityField->val('');
                    $entityField[0]['selectize']->setValue('');
                    $entityField[0]['selectize']->renderCache = [];
                    $entityField[0]['selectize']->clearOptions();
                    $entityField[0]['selectize']->addOption($entities);
                }
            }

            if($first) {
                $entityField->parents('.tab-pane')->addClass('active');
            }
            $first = false;
        }
        ?>
    </div>
</form>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer'); ?>
<a href="#close" class="btn" data-dismiss="modal">Cancel</a>
<a href="#submit-entities" class="btn btn-primary" data-dismiss="modal">Save</a>
<?php $view['slots']->stop();

// TODO: decide what to we do when it is extended?  what does jQuery($this) do?
if($dialog->length == 0) {
    //$dialog = $context->append($view['slots']->get('cell_status_pack'))->find('form');
}

