<?php

use StudySauce\Bundle\Entity\Coupon;

$view->extend('StudySauceBundle:Emails:layout.html.php');

$view['slots']->start('message'); ?>
Thank you for your recent purchase.  You can find the details of your order below.  If you have any questions, please feel free to contact us at:</p>
<p style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555;">
<a style="color:#FF9900;" href="mailto:admin@studysauce.com">admin@studysauce.com.</a></p>
<p style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555;">
<strong>Order Information:</strong><br />
<?php print $payment->getUser()->getFirst(); ?> <?php print $payment->getUser()->getLast(); ?></p>
<table border="0" style="border:0; width:100%;">
<?php foreach($payment->getCoupons()->toArray() as $c) {
/** @var Coupon $c */
?>
<tr>
<td style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555;"><?php print ($c->getDescription()); ?></td>
<td style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555;">$<?php print (number_format(array_values($c->getOptions())[0]['price'], 2)); ?></td>
</tr>
<?php } ?>
</table>
<p style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555;">
<strong>E-mail Address:</strong></p>
<p style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555;">
<a style="color:#FF9900;" href="mailto:<?php print $user->getEmail(); ?>"><?php print $user->getEmail(); ?></a></p>
<p style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555;">
<strong>Order Total:</strong>
$<?php print (number_format($payment->getAmount(), 2)); ?><br />
<?php $view['slots']->stop(); ?>