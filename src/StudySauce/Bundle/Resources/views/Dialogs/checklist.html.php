<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

 $view['slots']->start('modal-header') ?>
Study checklist
<?php $view['slots']->stop();

 $view['slots']->start('modal-body') ?>
<ol class="checkboxes">
    <li><label class="checkbox"><input type="checkbox" value="mobile"><i></i><span>Turn mobile device to airplane mode</span></label></li>
    <li><label class="checkbox"><input type="checkbox" value="distractions"><i></i><span>Minimize other electronic distractions (turn off TV, close FB, etc.)</span></label></li>
    <li><label class="checkbox"><input type="checkbox" value="materials"><i></i><span>Gather all of your study materials before starting</span></label></li>
    <li><label class="checkbox"><input type="checkbox" value="objective"><i></i><span>Understand your objective for the session (study a particular chapter, memorize key terms, etc.)</span></label></li>
    <li><label class="checkbox"><input type="checkbox" value="comfortable"><i></i><span>Get comfortable, but not too comfortable (try not to study on your bed)</span></label></li>
    <li><label class="checkbox"><input type="checkbox" value="hour"><i></i><span>Get ready to study for 1 hour, then you can take a short break</span></label></li>
</ol>
<?php $view['slots']->stop();

 $view['slots']->start('modal-footer') ?>
<a href="#study" class="btn btn-primary">Continue to session</a>
<?php $view['slots']->stop() ?>

