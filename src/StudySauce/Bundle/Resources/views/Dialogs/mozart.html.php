<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

 $view['slots']->start('modal-header') ?>
The Mozart Effect®
<?php $view['slots']->stop();

 $view['slots']->start('modal-body') ?>
<p>There is conflicting research about the benefits of listening to Mozart as a method for improving brain performance. Our best advice is to give it a shot and see if it works for you.</p>
<h3>Resources:</h3>
<div>
    <a href="http://www.mozarteffect.com/index.html" target="_blank">Dan Campbell's proprietary Mozart Effect®</a><br>
    <a href="http://lrs.ed.uiuc.edu/students/lerch1/edpsy/mozart_effect.html#The%20Mozart" target="_blank">Donna Lerch's article looking into several studies</a><br>
    <a href="https://musopen.org/" target="_blank">Music provided by MusOpen</a>
</div>
<?php $view['slots']->stop();
