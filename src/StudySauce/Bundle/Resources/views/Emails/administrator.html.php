<?php
$view->extend('StudySauceBundle:Emails:layout.html.php');

$view['slots']->start('message'); ?>
<table border="0" style="border:0; width: 100%;" width="100%"><tr><td><pre><?php print $properties; ?></pre></td></tr></table>
<?php $view['slots']->stop(); ?>
