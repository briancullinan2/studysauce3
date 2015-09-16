<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
    Log in faster in the future
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
    <div class="social-login">
        <br />
        <br />
        <?php $first = true;
        foreach($services as $o => $url) {
            if($o == 'evernote' || $o == 'gcal')
                continue;
            if(!$first) { ?>
                <div class="signup-or"><span>Or</span></div>
            <?php }
            $first = false; ?>
            <a href="<?php print $url; ?>?_target=<?php print $view['router']->generate($app->getRequest()->get('_route')); ?>" class="more">Connect</a>
        <?php } ?>
        <br />
        <br />
    </div>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>
<a href="#close" data-dismiss="modal">No thanks</a>
<?php $view['slots']->stop() ?>

