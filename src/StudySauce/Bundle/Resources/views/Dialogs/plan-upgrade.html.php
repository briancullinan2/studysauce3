<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Upgrade to premium and we will build your personalized study plan.
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<div class="highlighted-link">
    <a class="more parents" href="#bill-parents" data-toggle="modal">Bill my parents</a>
    <a href="<?php print $view['router']->generate('premium'); ?>" class="more">Go Premium</a>
</div>
<?php $view['slots']->stop();
