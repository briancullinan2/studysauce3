<?php

use StudySauce\Bundle\Entity\Coupon;
use StudySauce\Bundle\Entity\Invite;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\Payment;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\Entity\UserPack;



$view->extend('StudySauceBundle:Emails:layout.html.php');

/** @var Payment $payment */

$view['slots']->start('message'); ?>
Thank you for your recent purchase.  You can find the details of your order below.  If you have any questions, please feel free to contact us at:<br />
<a style="color:#FF9900;" href="mailto:admin@studysauce.com">admin@studysauce.com.</a><br /></p>
<p style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555;">
<strong>Order Information:</strong><br />
<?php print $payment->getUser()->getFirst(); ?> <?php print $payment->getUser()->getLast(); ?><br /><br />
</p>
<table border="0" style="border:0; width:100%;">
<?php
$subTotal = 0;
foreach($payment->getCoupons()->toArray() as $c) {
/** @var Coupon $c */
$price = array_values($c->getOptions())[0]['price'];
$subTotal += $price;
// find the last user to receive designation from packs in this coupon

$allUsers = [$payment->getUser()];
$packs = [];
$lastAssignee = null;
foreach($payment->getUser()->getInvites()->toArray() as $i) {
    /** @var Invite $i */
    if(empty($i->getInvitee())) {
        continue;
    }
    $allUsers[] = $i->getInvitee();
}
foreach($allUsers as $u) {
    /** @var User $u */
    foreach($c->getPacks()->toArray() as $cp) {
        /** @var Pack $cp */
        /** @var UserPack $up */
        if(!empty($up = $u->getUserPack($cp)) && (empty($packs[$cp->getId()]) || $up->getCreated() > max($packs))) {
            $packs[$cp->getId()] = $up->getCreated();
            $lastAssignee = $u;
        }
    }
}
?>
<tr>
<td style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555;"><?php print ($c->getDescription()); ?><?php
if(!empty($lastAssignee)) {
?><br />(<?php print ($lastAssignee->getFirst()); ?> <?php print ($lastAssignee->getLast()); ?>)<?php
} ?>
</td>
<td style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555; text-align: right;">&#36;<?php print (number_format($price, 2)); ?></td>
</tr>
<?php } ?>
<tr><td colspan="3"><hr /></td></tr>
<tr>
<td style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555;">Subtotal (<?php print (count($payment->getCoupons()->toArray())); ?> items):</td>
<td style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555; text-align: right;">&#36;<?php print (number_format($subTotal, 2)); ?></td>
</tr>
<tr>
<td style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555;">Tax:</td>
<td style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555; text-align: right;">&#36;<?php print (number_format(round($subTotal * .0795, 2), 2)); ?></td>
</tr>
<tr>
<td style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555;"><strong>Order Total:</strong></td>
<td style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555; text-align: right;">&#36;<?php print (number_format(round($subTotal + round($subTotal * .0795, 2), 2), 2)); ?></td>
</tr>
</table>
<?php $view['slots']->stop(); ?>