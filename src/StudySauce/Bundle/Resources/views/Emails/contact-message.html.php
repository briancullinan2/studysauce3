<?php
use StudySauce\Bundle\Entity\ContactMessage;

$view->extend('StudySauceBundle:Emails:layout.html.php');

/** @var ContactMessage $contact */

$view['slots']->start('message'); ?>
<strong>Name: </strong><?php print $view->escape($contact->getName()); ?><br />
<strong>Email: </strong><?php print $view->escape($contact->getEmail()); ?><br />
<strong>Message: </strong>
<hr />
<?php print $contact->getMessage(); ?>
<?php $view['slots']->stop(); ?>
