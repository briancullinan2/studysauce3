<?php
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;

/** @var GlobalVariables $app */

/** @var Card $card */
$context = !empty($context) ? $context : jQuery($this);

$rowHtml = jQuery($view->render('AdminBundle:Admin:row.html.php', [
    'tableId' => $tableId,
    'classes' => $classes,
    'entity' => $card,
    'table' => $table,
    'tables' => $tables,
    'request' => $request,
    'results' => $results,
    'context' => $context]));

$row = $context->filter(implode('', ['.', $table , '-id-', $card->getId()]));

if($row->length == 0) {
    $actual = $rowHtml->filter('[class*="-row"]');
}
else {
    $actual = $row->filter('[class*="-row"]');
}
if(!$actual->is(implode('', ['.card-row.type-' , $card->getResponseType()]))) {
    $actual->attr('class', preg_replace('/\\s*type-[^ ]*/i', ' ', $actual->attr('class')));
    if (!empty($card->getResponseType())) {
        $actual->addClass(implode('', ['type-' , $card->getResponseType()]));
    }
}

if($row->length == 0 || !$row->is('.edit')) {
    print(jQuery('<div />')->append($rowHtml)->html());
}
