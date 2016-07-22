<?php

use StudySauce\Bundle\Entity\Coupon;

$view->extend('StudySauceBundle:Emails:layout.html.php');

$view['slots']->start('message'); ?>
Thank you for your recent purchase.  You can find the details of your order below.  If you have any questions, please feel free to contact us at:<br />
<a style="color:#FF9900;" href="mailto:admin@studysauce.com">admin@studysauce.com.</a><br /></p>
<p style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555;">
    <strong>Order Information:</strong><br />
    <?php print $payment->getUser()->getFirst(); ?> <?php print $payment->getUser()->getLast(); ?><br /><br />
    <strong>E-mail Address:</strong><br />
    <a style="color:#FF9900;" href="mailto:<?php print $user->getEmail(); ?>"><?php print $user->getEmail(); ?></a>
</p>
<table border="0" style="border:0; width:100%;">
<?php
$subTotal = 0;
foreach($payment->getCoupons()->toArray() as $c) {
/** @var Coupon $c */
$price = array_values($c->getOptions())[0]['price'];
$subTotal += $price;
?>
<tr>
<td style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555;"><?php print ($c->getDescription()); ?></td>
<td style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555;">&#36;<?php print (number_format($price, 2)); ?></td>
</tr>
<?php } ?>
<tr><td colspan="3"><hr /></td></tr>
<tr>
<td style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555;">Subtotal (<?php print (count($payment->getCoupons()->toArray())); ?> items):</td>
<td style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555;">&#36;<?php print (number_format($subTotal, 2)); ?></td>
</tr>
<tr>
<td style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555;">Tax:</td>
<td style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555;">&#36;<?php print (number_format(round($subTotal * .0795, 2), 2)); ?></td>
</tr>
<tr>
<td style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555;"><strong>Order Total:</strong></td>
<td style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555;">&#36;<?php print (number_format(round($subTotal + round($subTotal * .0795, 2), 2), 2)); ?></td>
</tr>
</table>
<?php $view['slots']->stop(); ?>