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
foreach($results['coupon'] as $c) {
    /** @var Coupon $c */
    if(in_array($c->getName(), $cart)) {
        foreach($c->getOptions() as $o) {
            $subTotal += $o['price'];
            $last = $c;
        }
    }
}

$context = !empty($context) ? $context : jQuery($this);
if(count($cart) > 0) {
    $context->parents('body')->find('#welcome-message > a[href*="/store/cart"]')->text(count($cart));
}
else {
    $context->parents('body')->find('#welcome-message > a[href*="/store/cart"]')->html('&nbsp;');
}

if(!empty($last)) { ?>
<header><h3 class="highlighted-link">
        <label>Added to cart: <span><?php print ($last->getDescription()); ?></span></label>
        <strong>&#36;<?php print ($subTotal); ?></strong>
        <a href="<?php print ($view['router']->generate('checkout')); ?>" class="more">Checkout</a></h3></header>
<?php } ?>
