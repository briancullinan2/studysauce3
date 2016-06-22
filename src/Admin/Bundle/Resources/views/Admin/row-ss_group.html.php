<?php
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\Entity\UserPack;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;

/** @var GlobalVariables $app */
/** @var Group $ss_group */
$context = !empty($context) ? $context : jQuery($this);

$rowHtml = $view->render('AdminBundle:Admin:row.html.php', [
    'tableId' => $tableId,
    'classes' => $classes,
    'entity' => $ss_group,
    'table' => $table,
    'tables' => $tables,
    'request' => $request,
    'results' => $results,
    'context' => $context]);

$row = $context->filter(implode('', ['.', $table , '-id-', $ss_group->getId()]));

// skip rows that have zero retention

if(isset($request['notInGroup'])) {
    /** @var User $user */
    $user = $results['ss_user'][0];
    /**
     * @var Group $g2
     */
    foreach($user->getGroups()->toArray() as $j => $g2) {
        if($ss_group->getId() == $g2->getId()) {
            return;
        }
    }
}

if($row->length == 0 || !$row->is('.edit')) {
    print($rowHtml);
}
