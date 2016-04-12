<?php
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

$searchTables = [];

/** @var Group|Pack $entity */
foreach($fields as $subfield) {
    $joinTable = $table;
    $joinName = $table;
    $joinFields = explode('.', $subfield);
    foreach ($joinFields as $jf) {
        $associated = \Admin\Bundle\Controller\AdminController::$allTables[$joinTable]->getAssociationMappings();
        if (isset($associated[$jf])) {
            $te = $associated[$jf]['targetEntity'];
            $ti = array_search($te, \Admin\Bundle\Controller\AdminController::$allTableClasses);
            if ($ti !== false) {
                $joinTable = \Admin\Bundle\Controller\AdminController::$allTableMetadata[$ti]->table['name'];
            } else {
                continue;
            }
            $newName = $joinName . '_' . preg_replace('[^a-z]', '_', $jf) . $joinTable;
            $joinName = $newName;
        } else {
            // join failed, don't search any other tables this round
            $joinName = null;
            break;
        }
    }
    // do one search on the last entity on the join, ie not searching intermediate tables like user_pack or ss_user_group
    if (!empty($joinName) && isset(\Admin\Bundle\Controller\AdminController::$defaultTables[$joinTable])) {
        $searchTables[$joinTable] = \Admin\Bundle\Controller\AdminController::$defaultTables[$joinTable]['name'];
    }
}

if (count($searchTables) > 0 && method_exists($entity, 'get' . ucfirst($field))) {
    $result = $entity->{'get' . ucfirst($field)}();

    if ($result instanceof \Doctrine\Common\Collections\Collection) {
        print $this->render('AdminBundle:Admin:cell-collection.html.php', ['tables' => $searchTables, 'entities' => $result->toArray(), 'inline' => true]);
    }
}

