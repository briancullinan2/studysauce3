<?php
use Admin\Bundle\Controller\AdminController;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;

/** @var GlobalVariables $app */

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
<div class="results collapsible">
    <?php if (!$app->getRequest()->attributes->has('headers')) {
        print $view->render('AdminBundle:Admin:header-search.html.php', ['tables' => $tables, 'allGroups' => $allGroups] + compact(array_map(function ($t) {return $t . '_total';}, array_keys($tables))));
    }

    foreach ($tables as $table => $t) {
        $isNew = $app->getRequest()->get('new') === true || is_array($app->getRequest()->get('new')) && in_array($table, $app->getRequest()->get('new'));

        // show header template
        $tableTotal = $table . '_total';
        if (count($$table) > 0 || $isNew) {
            if(!$app->getRequest()->attributes->has('headers')) {
                print $view->render('AdminBundle:Admin:header.html.php', ['tables' => $tables, 'table' => $table, 'total' => $$tableTotal, 'allGroups' => $allGroups]);
            }
            else if (is_array($headers = $app->getRequest()->attributes->get('headers'))
                && isset($headers[$table])
                && $view->exists('AdminBundle:Admin:header-' . $headers[$table] . '.html.php')) {
                print $view->render('AdminBundle:Admin:header-' . $headers[$table] . '.html.php', ['tables' => $tables, 'table' => $table, 'total' => $$tableTotal, 'allGroups' => $allGroups]);
            }
        }

        // print out all result entities
        foreach ($$table as $entity) {
            print $view->render('AdminBundle:Admin:row.html.php', ['entity' => $entity, 'tables' => $tables, 'table' => $table, 'allGroups' => $allGroups]);
        }

        // print out row template for client side to use
        $class = AdminController::$allTables[$table]->name;
        $entity = new $class();
        if($isNew) {
            $classes = ' empty';
            $newCount = $isNew && !empty(intval($app->getRequest()->get('count-' . $table))) ? intval($app->getRequest()->get('count-' . $table)) : 1;
            for ($nc = 0; $nc < $newCount; $nc++) {
                print $view->render('AdminBundle:Admin:row.html.php', ['classes' => $classes, 'entity' => $entity, 'tables' => $tables, 'table' => $table, 'allGroups' => $allGroups]);
            }
        }
        $classes = 'template empty';
        print $view->render('AdminBundle:Admin:row.html.php', ['classes' => $classes, 'entity' => $entity, 'tables' => $tables, 'table' => $table, 'allGroups' => $allGroups]);

        // show footer at the end of each result list
        if (count($$table) > 0 || $isNew) {
            if(!$app->getRequest()->attributes->has('footers')) {
                print $view->render('AdminBundle:Admin:footer.html.php', ['table' => $table, 'total' => $$tableTotal]);
            }
            else if (is_array($headers = $app->getRequest()->attributes->get('footers'))
                && isset($headers[$table])
                && $view->exists('AdminBundle:Admin:footer-' . $headers[$table] . '.html.php')) {
                print $view->render('AdminBundle:Admin:footer-' . $headers[$table] . '.html.php', ['table' => $table, 'total' => $$tableTotal]);
            }
        }
    } ?>
</div>