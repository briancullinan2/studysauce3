<div class="modal" id="<?php print ($id); ?>" tabindex="-1" role="dialog" aria-hidden="true" <?php print (isset($attributes) ? $attributes : ''); ?>>
    <div class="modal-dialog">
        <div class="modal-content">
            <?php if($view['slots']->get('modal-header') != null) { ?>
            <div class="modal-header">
                <a href="#close" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></a>
                <?php $view['slots']->output('modal-header'); ?>
            </div>
            <?php } ?>
            <?php if($view['slots']->get('modal-body') != null) { ?>
            <div class="modal-body">
                <?php $view['slots']->output('modal-body'); ?>
            </div>
            <?php } ?>
            <?php if($view['slots']->get('modal-footer') != null) { ?>
            <div class="modal-footer">
                <?php $view['slots']->output('modal-footer'); ?>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php
$view['slots']->start('modal-header');
$view['slots']->stop();
$view['slots']->start('modal-body');
$view['slots']->stop();
$view['slots']->start('modal-footer');
$view['slots']->stop();
