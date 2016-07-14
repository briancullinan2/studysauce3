<?php
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\Coupon;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\Entity\UserPack;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;

/** @var GlobalVariables $app */
/** @var Coupon $coupon */
$context = !empty($context) ? $context : jQuery($this);

if(isset($request['inCartOnly'])) {
    $httpRequest = $app->getRequest();
    $cart = explode(',', $httpRequest->cookies->get('cart'));
    if(!in_array($coupon->getName(), $cart)) {
        return;
    }
}

$rowHtml = $view->render('AdminBundle:Admin:row.html.php', [
    'tableId' => $tableId,
    'classes' => $classes,
    'entity' => $coupon,
    'table' => $table,
    'tables' => $tables,
    'request' => $request,
    'results' => $results,
    'context' => $context]);

$row = $context->filter(implode('', ['.', $table , '-id-', $coupon->getId()]));

if($row->length == 0 || !$row->is('.edit')) {
    print($rowHtml);
}
