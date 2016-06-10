<?php
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\Entity\UserPack;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;

/** @var GlobalVariables $app */

/** @var Pack $pack */
$context = !empty($context) ? $context : jQuery($this);

$rowHtml = $view->render('AdminBundle:Admin:row.html.php', [
    'tableId' => $tableId,
    'classes' => $classes,
    'entity' => $pack,
    'table' => $table,
    'tables' => $tables,
    'request' => $request,
    'results' => $results,
    'context' => $context]);

$row = $context->filter(implode('', ['.', $table , '-id-', $pack->getId()]));

// skip rows that have zero retention
/** @var UserPack $up */
if(isset($request['user_pack-removed']) && (strpos($rowHtml, '<label>0</label>') !== false
    || empty($up = $results['ss_user'][0]->getUserPack($pack)) || $up->getRemoved()
        || $up->getPack()->getStatus() == 'DELETED' || $up->getPack()->getStatus() == 'UNPUBLISHED')) {
    return;
}

if($row->length == 0 || !$row->is('.edit')) {
    print($rowHtml);
}
