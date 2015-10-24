<?php
use StudySauce\Bundle\Entity\Group;

$view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Use the list below to manage groups.
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<table class="results">
    <thead>
    <tr>
        <th><span><a href="#add-group">Add Group</a></span><br />Name</th>
        <th>Description</th>
        <th>Roles</th>
        <th>Users</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php
    if(empty($groups))
        $groups = [new Group()];
    foreach($groups as $i => $g) {
        /** @var Group $g */
        ?>
    <tr class="group-id-<?php print $g->getId(); ?> read-only">
        <td><label class="input"><input type="text" name="groupName" value="<?php print $view->escape($g->getName()); ?>" /></label></td>
        <td><label class="input"><textarea name="description"><?php print $view->escape($g->getDescription()); ?></textarea></label></td>
        <td>
            <label class="checkbox"><input type="checkbox" name="roles" value="ROLE_PAID" <?php print ($g->hasRole('ROLE_PAID') ? 'checked="checked"' : ''); ?> /><i></i><span>PAID</span></label>
            <label class="checkbox"><input type="checkbox" name="roles" value="ROLE_ADMIN" <?php print ($g->hasRole('ROLE_ADMIN') ? 'checked="checked"' : ''); ?> /><i></i><span>ADMIN</span></label>
            <label class="checkbox"><input type="checkbox" name="roles" value="ROLE_PARENT" <?php print ($g->hasRole('ROLE_PARENT') ? 'checked="checked"' : ''); ?> /><i></i><span>PARENT</span></label>
            <label class="checkbox"><input type="checkbox" name="roles" value="ROLE_PARTNER" <?php print ($g->hasRole('ROLE_PARTNER') ? 'checked="checked"' : ''); ?> /><i></i><span>PARTNER</span></label>
            <label class="checkbox"><input type="checkbox" name="roles" value="ROLE_ADVISER" <?php print ($g->hasRole('ROLE_ADVISER') ? 'checked="checked"' : ''); ?> /><i></i><span>ADVISER</span></label>
            <label class="checkbox"><input type="checkbox" name="roles" value="ROLE_MASTER_ADVISER" <?php print ($g->hasRole('ROLE_MASTER_ADVISER') ? 'checked="checked"' : ''); ?> /><i></i><span>MASTER_ADVISER</span></label>
        </td>
        <td><?php print $g->getUsers()->count(); ?></td>
        <td>
            <a href="#edit-group"></a>
            <a href="#cancel-edit">Cancel</a><br />
            <a href="#save-group">Save</a>
        </td>
    </tr>
    <?php } ?>
    </tbody>
</table>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer'); ?>
<a href="#close" class="btn btn-primary" data-dismiss="modal">Done</a>
<?php $view['slots']->stop() ?>

