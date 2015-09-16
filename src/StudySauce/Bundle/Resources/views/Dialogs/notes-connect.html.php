<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Backup your notes with Evernote
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<span class="site-name"><?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/Study_Sauce_Logo_Sketch.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
        <img width="100" height="100" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
    <?php endforeach; ?><strong>Study</strong> Sauce</span>
<div class="connect-or"><span>+</span></div>
<span class="evernote-name"><?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/logotentips-7.jpg'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
        <img width="240" height="165" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
    <?php endforeach; ?></span>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>
<?php foreach($services as $o => $url) { ?>
    <a href="<?php print $url; ?>?_target=<?php print $view['router']->generate('notes'); ?>" class="btn">Connect Evernote</a>
<?php } ?>
<?php $view['slots']->stop() ?>

