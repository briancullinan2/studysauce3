<?php
$view->extend('StudySauceBundle:Emails:layout.html.php');

$view['slots']->start('message'); ?>
<pre><?php print $properties; ?></pre>
<?php $view['slots']->stop(); ?>
