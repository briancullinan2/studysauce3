<?php

$view->extend('StudySauceBundle:Emails:layout.html.php');

$view['slots']->start('message'); ?>
Thank you for your recent purchase.  You can find the details of your order below.  If you have any questions, please feel free to contact us at:</p>
<p style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555;">
<a style="color:#FF9900;" href="mailto:admin@studysauce.com">admin@studysauce.com.</a></p>
<p style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555;">
<strong>Purchasing Information:</strong><br />
<?php print $payment->getFirst(); ?> <?php print $payment->getLast(); ?></p>
<p style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555;">
<strong>Billing Address</strong><br />
<?php print $address; ?></p>
<p style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555;">
<strong>E-mail Address:</strong></p>
<p style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555;">
<a style="color:#FF9900;" href="mailto:<?php print $user->getEmail(); ?>"><?php print $user->getEmail(); ?></a></p>
<p style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555;">
<strong>Order Total:</strong>
<?php print $payment->getAmount(); ?><br />
<?php $view['slots']->stop(); ?>