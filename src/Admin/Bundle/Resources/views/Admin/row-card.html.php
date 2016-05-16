<?php
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;

/** @var GlobalVariables $app */

/** @var Card $card */

$subVars = [
    'request' => $request,
    'results' => $results
];

$context = !empty($context) ? $context : jQuery($this);

$row = jQuery($view->render('AdminBundle:Admin:row.html.php', array_merge($subVars, [
    'tableId' => $tableId,
    'classes' => $classes,
    'entity' => $card,
    'table' => $table,
    'tables' => $tables,
    'request' => $request,
    'results' => $results])));

$actual = $row->filter('[class*="-row"]');
if(!$actual->is(implode('', ['.type-' , $card->getResponseType()]))) {
    $actual->attr('class', preg_replace('/\s*type-.*?(\s|$)/i', ' ', $actual->attr('class')));
    if (!empty($card->getResponseType())) {
        $actual->addClass(implode('', ['type-' , $card->getResponseType()]));
    }
}

print($row);
