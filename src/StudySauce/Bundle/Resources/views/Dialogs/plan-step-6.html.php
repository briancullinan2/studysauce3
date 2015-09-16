<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Step 6 - Download your new study plan
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<div class="google-sync highlighted-link">
    <p><strong>Sync with your Google Calendar</strong><br />(Recommended)</p>
    <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/Googlelogo.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
        <img height="100" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
    <?php endforeach; ?>
    <?php foreach($services as $o => $url) { ?>
        <a href="<?php print $url; ?>?_target=<?php print $view['router']->generate('plan'); ?>" class="more">Connect</a>
    <?php } ?>
</div>
<div class="manual-download">
    <p><strong>Don't use Google Calendar?</strong><br />(Download below)</p>
    <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/plan-download-white.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
            <img height="100" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
    <?php endforeach; ?>
    <a href="<?php print $view['router']->generate('plan_download'); ?>" class="more">Download</a>
</div>
<br/><br/><br/>
<div class="highlighted-link">
    <ul class="dialog-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
</div>
<?php $view['slots']->stop();
