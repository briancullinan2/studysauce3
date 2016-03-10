<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Drag a file here -or-
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<div class="plupload">
    <div class="plup-filelist">
        <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/upload_all.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
            <img width="300" height="100" src="<?php echo $view->escape($url) ?>" alt="Upload" />
        <?php endforeach; ?>
    </div>
    <a href="#file-select" class="plup-select" id="file-upload-select">Click here to select an image</a>
    <input type="hidden" name="">
</div>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>
<a href="#submit-upload" class="btn btn-primary" data-dismiss="modal">Save</a>
<?php $view['slots']->stop() ?>
