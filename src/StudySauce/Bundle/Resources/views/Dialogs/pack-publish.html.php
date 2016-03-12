<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-body') ?>
<label class="radio">
    <input type="radio" name="publish-schedule" value="now" />
    <i></i>
    <span>Publish now</span>
</label><br/>
<label class="radio">
    <input type="radio" name="publish-schedule" value="later" checked="checked" />
    <i></i>
    <span>Publish later</span>
</label>
</<label class="input">
    <input type="text" name="publish-date" placeholder="Date/time" />
</label>
<div id="publish-date"></div>
<h3>Notifications:</h3>
<label class="checkbox">
    <input type="checkbox" name="publish-schedule" value="now" checked="checked" />
    <i></i>
    <span>Email sent to user when pack publishes</span>
</label><br/>
<label class="checkbox">
    <input type="checkbox" name="publish-schedule" value="later" checked="checked" />
    <i></i>
    <span>In-app alert sent when pack publishes</span>
</label>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>
<a href="#close" class="btn btn-primary" data-dismiss="modal">Publish</a>
<?php $view['slots']->stop() ?>
