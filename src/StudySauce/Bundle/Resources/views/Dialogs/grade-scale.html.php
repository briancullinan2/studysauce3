<?php use StudySauce\Bundle\Controller\CalcController;

$view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Grade Scale at your school
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<ul class="nav nav-tabs">
    <?php
    $first = true;
    foreach(array_keys(CalcController::$presets) as $k) { ?>
        <li class="<?php print ($first ? 'active' : ''); ?>"><a href="#scale-preset"><?php print $k; ?></a></li>
    <?php $first = false; } ?>
</ul>
<table>
    <thead>
    <tr>
        <th></th>
        <th>High</th>
        <th>Low</th>
        <th>Grade point</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for($i = 0; $i < count($scale); $i++) {
        if (empty($scale[$i]) || count($scale[$i]) < 4 || empty($scale[$i][0])) {
            continue;
        } ?>
        <tr>
            <td><label class="input"><input type="text" value="<?php print $scale[$i][0]; ?>"/></label></td>
            <td><label class="input"><input type="text" value="<?php print $scale[$i][1]; ?>"/></label></td>
            <td><label class="input"><input type="text" value="<?php print $scale[$i][2]; ?>"/></label></td>
            <td><label class="input"><input type="text"
                                            value="<?php print number_format($scale[$i][3], 2); ?>"/></label></td>
        </tr>
    <?php } ?>
    </tbody>
</table>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>
<a href="#save-scale" class="btn btn-primary">Save</a>
<?php $view['slots']->stop() ?>

