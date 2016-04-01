<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-body') ?>
<form action="<?php print $view['router']->generate('command_callback'); ?>" method="post">
    <h2>Add or Remove </h2>
    <ul class="nav nav-tabs">
        <li class="active template">
            <a href="#add-entity-template" data-target="#add-entity-template" data-toggle="tab">Template</a></li>
    </ul>
    <div class="tab-content">
        <div id="add-entity-template" class="tab-pane active template">
            <?php print $this->render('AdminBundle:Admin:row-collection.html.php', ['tables' => [], 'entities' => []]); ?>
        </div>
    </div>
</form>
<?php $view['slots']->stop();


$view['slots']->start('modal-footer') ?>
<a href="#submit-entities" class="btn btn-primary" data-dismiss="modal">Save</a>
<?php $view['slots']->stop() ?>