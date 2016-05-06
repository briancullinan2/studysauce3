<?php
use StudySauce\Bundle\Entity\User;

/** @var User $user */
$user = $app->getUser();

$view->extend('AdminBundle:Admin:dialog.html.php');

$view['slots']->start('modal-header') ?>
    Give your notebook a name.
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
    <p>Classes will automatically create notebooks when notes are saved in them.
        <br />
        <br /></p>
    <form action="<?php print $view['router']->generate('notes_notebook'); ?>" method="post">
        <div class="notebook-name">
            <label class="input"><span>Notebook name</span>
                <input type="text" value=""></label>
        </div>
        <div class="highlighted-link invalid clearfix">
            <br/>
            <button type="submit" value="#save-notebook" class="more">Create</button>
            <br />
        </div>
    </form>
<?php $view['slots']->stop();
