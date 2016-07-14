<?php

use StudySauce\Bundle\Entity\Coupon;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;

/** @var GlobalVariables $app */
$httpRequest = $app->getRequest();
$cart = explode(',', $httpRequest->cookies->get('cart'));
if($cart[0] == '') {
    array_splice($cart, 0, 1);
}

$subTotal = 0;
$newCart = [];
foreach($results['coupon'] as $c) {
    /** @var Coupon $c */
    if(in_array($c->getName(), $cart)) {
        foreach($c->getOptions() as $o) {
            $subTotal += $o['price'];
            $newCart[count($newCart)] = $c->getName();
        }
    }
}

$httpRequest->cookies->set('cart', implode(',', $newCart));

$context = !empty($context) ? $context : jQuery($this);
if(count($cart) > 0) {
    $context->parents('body')->find('#welcome-message > a[href*="/store/cart"]')->text(count($newCart));
}
else {
    $context->parents('body')->find('#welcome-message > a[href*="/store/cart"]')->html('&nbsp;');
}

?>
<div class="highlighted-link form-actions">
    <table><tbody>
        <tr><td>Subtotal (<?php print (count($newCart)); ?> items):</td><td>&#36;<?php print ($subTotal); ?></td></tr>
        <tr><td>Tax</td><td>&#36;<?php print (round($subTotal * .0795, 2)); ?></td></tr>
        </tbody>
        <tfoot>
        <tr><td>Total</td><td>&#36;<?php print ($subTotal + round($subTotal * .0795, 2)); ?></td></tr>
        </tfoot>
    </table>
    <a class="more" href="<?php print ($view['router']->generate('checkout')); ?>">Place order</a>
</div>