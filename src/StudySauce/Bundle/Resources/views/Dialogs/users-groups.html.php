<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-body') ?>
<h2>Authorized to see this pack:</h2>
    <form action="<?php print $view['router']->generate('command_callback'); ?>" method="post">
        <h3>Groups:</h3>
        <?php print $this->render('AdminBundle:Admin:cell-collection.html.php', ['tables' => ['ss_group' => ['name', 'userCountStr', 'description', 'id']], 'entities' => []]); ?>
        <h3>Individuals:</h3>
        <?php print $this->render('AdminBundle:Admin:cell-collection.html.php', ['tables' => ['ss_user' => ['first', 'last', 'email']], 'entities' => []]); ?>
    </form>
<?php $view['slots']->stop();


$view['slots']->start('modal-footer') ?>
<a href="#submit-entities" class="btn btn-primary" data-dismiss="modal">Save</a>
<?php $view['slots']->stop() ?>
