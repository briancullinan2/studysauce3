<?php $view->extend('AdminBundle:Admin:dialog.html.php', ['id' => 'pack-publish']);

$view['slots']->start('modal-body'); ?>
<label class="radio">
    <input type="radio" name="date" value="now" />
    <i></i>
    <span>Publish now</span>
</label><br/>
<label class="radio">
    <input type="radio" name="date" value="later" checked="checked" />
    <i></i>
    <span>Publish later</span>
</label><br />
<label class="input">
    <input type="text" name="schedule" placeholder="Date/time" />
</label>
<h3>Notifications:</h3>
<label class="checkbox">
    <input type="checkbox" name="email" value="true" checked="checked" />
    <i></i>
    <span>Email sent to user when pack publishes</span>
</label><br/>
<label class="checkbox">
    <input type="checkbox" name="alert" value="true" checked="checked" />
    <i></i>
    <span>In-app alert sent when pack publishes</span>
</label>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer'); ?>
<a href="#submit-publish" class="btn btn-primary" data-dismiss="modal">Publish</a>
<?php $view['slots']->stop(); ?>
