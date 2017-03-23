<?php
use StudySauce\Bundle\Entity\User;

$view->extend('AdminBundle:Admin:dialog.html.php');

$view['slots']->start('modal-header'); ?>
<h2>Create a bundle</h2>
<?php $view['slots']->stop();


$view['slots']->start('modal-body'); ?>
<form action="<?php print ($view['router']->generate('command_save')); ?>" method="post">
    <label class="input">
        <span>Title</span><br />
        <input type="text" name="description" value="" />
    </label>
    <label class="input">
        <span>Price</span><br />
        <select name="options">
            <option value="">- Select a price point -</option>
            <option value="<?php print ($view->escape('a:1:{s:8:"GHCB1617";a:1:{s:5:"price";d:29.99;}}')); ?>">29.99 (Great Hearts Bundle)</option>
        </select>
    </label>
    <div class="entity-search pack">
        <header>
            <label><span>Packs</span></label>
        </header>
        <label class="input">
            <input type="text" placeholder="Search for a pack" name="packs" data-tables="<?php
            $tables = (array)(new stdClass());
            $tables['pack'] = ['title', 'id', 'status'];
            print ($view->escape(json_encode($tables))); ?>" value="" data-confirm="false" /></label>
    </div>
    <div class="highlighted-link">
        <label class="checkbox another">
            <input type="checkbox" name="addAnother" value="1" /><i></i>Add another bundle
        </label>
        <a href="#close" class="btn" data-dismiss="modal">Cancel</a>
        <button type="submit" href="#submit" class="btn btn-primary">Add</button>
    </div>
</form>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer'); ?>
<?php $view['slots']->stop(); ?>
