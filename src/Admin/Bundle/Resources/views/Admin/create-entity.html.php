<?php

use Admin\Bundle\Controller\AdminController;

$view->extend('AdminBundle:Admin:dialog.html.php', ['id' => 'create-entity']);

$context = jQuery($this);
$dialog = $context->find('#create-entity');

$tableName = array_keys($tables)[0];

$view['slots']->start('modal-header'); ?>
    <h3>Create a new <?php print (ucfirst(str_replace('ss_', '', $tableName))); ?></h3>
<?php $view['slots']->stop();

if($tableName == 'pack') {
    $newPath = $view['router']->generate('packs_new');
}
if($tableName == 'ss_group') {
    $newPath = $view['router']->generate('groups_new');
}

// TODO copy settings to add-entity dialog also!

$view['slots']->start('modal-body'); ?>
    <a href="<?php print ($newPath); ?>" class="cloak"><span class="reveal">Start from scratch</span></a>
    <a href="#add-entity" data-target="#add-entity" data-toggle="modal" class="cloak"><span class="reveal">Find existing <?php print (str_replace('ss_', '', $tableName)); ?>s</span></a>
<?php $view['slots']->stop();

if($dialog->length > 0) {
    //$dialog = $context->append($view['slots']->get('cell_status_pack'))->find('form');
    $dialog->find('h3')->remove();
    $dialog->find('.modal-header')->append(jQuery($view['slots']->get('modal-header')));
    $dialog->find('a')->remove();
    $dialog->find('.modal-body')->append(jQuery($view['slots']->get('modal-body')));
}

