<?php
use Admin\Bundle\Controller\AdminController;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;

/** @var GlobalVariables $app */

$subVars = ['tables' => $tables, 'allGroups' => $allGroups, 'searchRequest' => $searchRequest] + compact(array_map(function ($t) {return $t . '_total';}, array_keys($tables)));

?>
<style>
    <?php foreach($tables as $table => $t) { ?>
    .showing-<?php print $table; ?> header > .<?php print $table; ?> {
        display: inline-block;
        opacity: 1;
        visibility: visible;
    }

    .showing-<?php print $table; ?> header > h2.<?php print $table; ?> {
        display: block;
        opacity: 1;
        visibility: visible;
    }

    .results .<?php print $table; ?>-row.edit ~ .highlighted-link.<?php print $table; ?> a[href^="#edit-"],
    .results.collapsible > h2.<?php print $table; ?>.collapsed ~ .highlighted-link.<?php print $table; ?>,
    .results.collapsible > h2.<?php print $table; ?>.collapsed ~ .<?php print $table; ?>-row {
        display: none;
    }

    <?php } ?>
</style>
<div class="results <?php print (isset($searchRequest['classes']) && is_array($searchRequest['classes']) ? implode(' ', $searchRequest['classes']) : ''); ?>" data-request="<?php print $view->escape(json_encode($searchRequest)); ?>">
    <?php if (!isset($searchRequest['headers'])) {
        print $view->render('AdminBundle:Admin:header-search.html.php', $subVars);
    }

    foreach ($tables as $table => $t) {
        $isNew = isset($searchRequest['new']) && ($searchRequest['new'] === true || is_array($searchRequest['new']) && in_array($table, $searchRequest['new']));

        // show header template
        $tableTotal = $table . '_total';
        if (count($$table) > 0 || $isNew) {
            if(!isset($searchRequest['headers'])) {
                print $view->render('AdminBundle:Admin:header.html.php', $subVars + ['table' => $table]);
            }
            else if (is_array($headers = $searchRequest['headers'])
                && isset($headers[$table])
                && $view->exists('AdminBundle:Admin:header-' . $headers[$table] . '.html.php')) {
                print $view->render('AdminBundle:Admin:header-' . $headers[$table] . '.html.php', $subVars + ['table' => $table]);
            }
        }

        // print out all result entities
        foreach ($$table as $entity) {
            print $view->render('AdminBundle:Admin:row.html.php', $subVars + ['entity' => $entity, 'table' => $table]);
        }

        // print out row template for client side to use
        $class = AdminController::$allTables[$table]->name;
        $entity = new $class();
        if($isNew) {
            $classes = ' empty';
            $newCount = $isNew && !empty(intval($searchRequest['count-' . $table])) ? intval($searchRequest['count-' . $table]) : 1;
            for ($nc = 0; $nc < $newCount; $nc++) {
                print $view->render('AdminBundle:Admin:row.html.php', $subVars + ['classes' => $classes, 'entity' => $entity, 'table' => $table]);
            }
        }
        $classes = 'template empty';
        print $view->render('AdminBundle:Admin:row.html.php', $subVars + ['classes' => $classes, 'entity' => $entity, 'table' => $table]);

        // show footer at the end of each result list
        if(!isset($searchRequest['footers'])) {
            print $view->render('AdminBundle:Admin:footer.html.php', $subVars + ['table' => $table, $table => $$table]);
        }
        else if (is_array($headers = $searchRequest['footers'])
            && isset($headers[$table])
            && $view->exists('AdminBundle:Admin:footer-' . $headers[$table] . '.html.php')) {
            print $view->render('AdminBundle:Admin:footer-' . $headers[$table] . '.html.php', $subVars + ['table' => $table, $table => $$table]);
        }
    } ?>
</div>