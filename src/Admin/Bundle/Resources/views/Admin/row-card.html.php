<?php
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;

/** @var GlobalVariables $app */

/** @var Card $card */
$context = !empty($context) ? $context : jQuery($this);

$subVars = [
    'request' => $request,
    'results' => $results
];

$context = !empty($context) ? $context : jQuery($this);

$rowHtml = jQuery($view->render('AdminBundle:Admin:row.html.php', array_merge($subVars, [
    'tableId' => $tableId,
    'classes' => $classes,
    'entity' => $card,
    'table' => $table,
    'tables' => $tables,
    'request' => $request,
    'results' => $results])));

$row = $context->find(implode('', ['.', $table , '-id-', $card->getId()]));

if($row->length == 0) {
    $actual = $rowHtml->filter('[class*="-row"]');
}
else {
    $actual = $row->filter('[class*="-row"]');
}
if(!$actual->is(implode('', ['.card-row.type-' , $card->getResponseType()]))) {
    $actual->attr('class', preg_replace('/\\s*type-.*?\\s/i', ' ', $actual->attr('class')));
    if (!empty($card->getResponseType())) {
        $actual->addClass(implode('', ['type-' , $card->getResponseType()]));
    }
}

print(jQuery('<div />')->append($rowHtml)->html());
