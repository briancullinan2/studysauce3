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

// skip rows that are not in the users group

if(isset($request['notInGroup'])) {
    $isInGroup = false;
    /** @var User $user */
    $user = $results['ss_user'][0];
    /**
     * @var Group $g2
     */
    foreach($user->getGroups()->toArray() as $j => $g2) {
        if($ss_group->getId() == $g2->getId()) {
            $isInGroup = true;
            break;
        }
    }
    if(!$isInGroup || $ss_group->getDeleted()) {
        return;
    }
}

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

if($row->length == 0 || !$row->is('.edit')) {
    print($rowHtml);
}
