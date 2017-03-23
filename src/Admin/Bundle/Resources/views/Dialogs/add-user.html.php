<?php
use StudySauce\Bundle\Entity\Group;

$view->extend('AdminBundle:Admin:dialog.html.php');

$view['slots']->start('modal-header') ?>
Create a new user.
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<div class="first-name">
    <label class="input"><input type="text" placeholder="First name" value="<?php print (isset($first) ? $first : ''); ?>"></label>
</div>
<div class="last-name">
    <label class="input"><input type="text" placeholder="Last name" value="<?php print (isset($last) ? $last : ''); ?>"></label>
</div>
<div class="email">
    <label class="input"><input type="text" placeholder="Email" value="<?php print (isset($email) ? $email : ''); ?>"></label>
</div>
<div class="password">
    <label class="input"><input type="password" placeholder="Enter password" value=""></label>
</div>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer'); ?>
<a href="#close" class="btn" data-dismiss="modal">Cancel</a>
<a href="#add-user" class="btn btn-primary" data-dismiss="modal">Save</a>
<?php $view['slots']->stop() ?>

