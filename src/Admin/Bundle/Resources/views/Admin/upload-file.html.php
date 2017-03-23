<?php $view->extend('AdminBundle:Admin:dialog.html.php', ['id' => 'upload-file']);

$view['slots']->start('modal-header'); ?>
<h3>Upload an image</h3>
<?php $view['slots']->stop();

$view['slots']->start('modal-body'); ?>
<div class="plupload">
    <div class="plup-filelist">
        <img width="300" height="100" src="<?php print ($view->escape($view['assets']->getUrl('bundles/studysauce/images/upload_all.png'))); ?>" alt="Upload" class="centerized default" />
        <a href="#file-select" class="plup-select" id="file-upload-select">Drag image here or click to select (1GB max)</a>
    </div>
    <input type="hidden" name="">
</div>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer'); ?>
<a href="#close" class="btn" data-dismiss="modal">Cancel</a>
<a href="#submit-upload" class="btn btn-primary" data-dismiss="modal">Save</a>
<?php $view['slots']->stop(); ?>
