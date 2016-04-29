<?php
use Admin\Bundle\Controller\AdminController;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;

/** @var GlobalVariables $app */

$subVars = array_merge(['tables' => $tables, 'allGroups' => $allGroups, 'searchRequest' => $searchRequest, 'results' => $results], compact(array_map(function ($t) {
        return $t . '_total';
    }, array_keys($tables))));
?>
<div
    class="results <?php print (isset($searchRequest['classes']) && is_array($searchRequest['classes']) ? implode(' ', $searchRequest['classes']) : ''); ?>"
    data-request="<?php print $view->escape(json_encode($searchRequest)); ?>">
    <?php if (!isset($searchRequest['headers'])) {
        print $view->render('AdminBundle:Admin:header-search.html.php', $subVars);
    }

    if($app->getUser()->getEmailCanonical() == 'brian@studysauce.com') {
        if(!empty($searchRequest['views'])) { ?><ul class="views"><?php
            foreach($searchRequest['views'] as $v => $extend) {
                ?><li><a href="#switch-view-<?php print $v; ?>"><?php print $v; ?></a></li><?php
            } ?></ul><?php
        }
        else { ?>
            <ul class="views"><li><a href="#switch-view-" data-extend="{}">Refresh</a></li></ul>
        <?php }
    }

    foreach ($tables as $table => $t) {
        $tableParts = explode('-', $table);
        $ext = implode('-', array_splice($tableParts, 1));
        $table = explode('-', $table)[0];
        $aliasedRequest = [];
        if(strlen($ext) > 0) {
            $ext = '-' . $ext;
            $aliasLen = strlen($table) + strlen($ext);
            foreach ($searchRequest as $r => $s) {
                if (substr($r, 0, $aliasLen) == $table . $ext) {
                    $aliasedRequest[substr($r, $aliasLen)] = $s;
                }
            }
            $aliasedRequest['tables'][$table] = $searchRequest['tables'][$table . $ext];
        }
        $aliasedRequest = array_merge($searchRequest, $aliasedRequest);
        $subVars = array_merge($subVars, ['searchRequest' => $aliasedRequest]);

        $isNew = isset($aliasedRequest['new']) && ($aliasedRequest['new'] === true || is_array($aliasedRequest['new']) && in_array($table, $aliasedRequest['new']));

        // show header template
        $tableTotal = $table . '_total';
        if (count($results[$table . $ext]) > 0 || $isNew) {
            if (!isset($aliasedRequest['headers']) || is_array($headers = $aliasedRequest['headers'])
                && isset($headers[$table]) && $headers[$table] === true
            ) {
                print $view->render('AdminBundle:Admin:header.html.php',                          array_merge($subVars, ['table' => $table]));
            } else if (is_array($headers = $aliasedRequest['headers'])
                && isset($headers[$table])
                && $view->exists('AdminBundle:Admin:header-' . $headers[$table] . '.html.php')
            ) {
                print $view->render('AdminBundle:Admin:header-' . $headers[$table] . '.html.php', array_merge($subVars, ['table' => $table]));
            }
        }

        // print out all result entities
        foreach ($results[$table . $ext] as $entity) {
            if ($view->exists('AdminBundle:Admin:row-' . $table . '.html.php')) {
                print $view->render('AdminBundle:Admin:row-' . $table . '.html.php', array_merge($subVars, [$table => $entity, 'table' => $table]));
            } else {
                print $view->render('AdminBundle:Admin:row.html.php',                array_merge($subVars, ['entity' => $entity, 'table' => $table]));
            }
        }

        // print out row template for client side to use
        $class = AdminController::$allTables[$table]->name;
        $entity = new $class();
        if ($isNew) {
            $classes = ' empty';
            $newCount = $isNew && !empty(intval($aliasedRequest['count-' . $table])) ? intval($aliasedRequest['count-' . $table]) : 1;
            for ($nc = 0; $nc < $newCount; $nc++) {
                if ($view->exists('AdminBundle:Admin:row-' . $table . '.html.php')) {
                    print $view->render('AdminBundle:Admin:row-' . $table . '.html.php', array_merge($subVars, ['classes' => $classes, $table => $entity, 'table' => $table]));
                } else {
                    print $view->render('AdminBundle:Admin:row.html.php',                array_merge($subVars, ['classes' => $classes, 'entity' => $entity, 'table' => $table]));
                }
            }
        }
        $classes = 'template empty';
        $templateSubVars = $subVars;
        $templateSubVars['searchRequest'] = array_merge($templateSubVars['searchRequest'], ['read-only' => false, 'edit' => false]);
        if ($view->exists('AdminBundle:Admin:row-' . $table . '.html.php')) {
            print $view->render('AdminBundle:Admin:row-' . $table . '.html.php', array_merge($templateSubVars, ['classes' => $classes, $table => $entity, 'table' => $table]));
        } else {
            print $view->render('AdminBundle:Admin:row.html.php',                array_merge($templateSubVars, ['classes' => $classes, 'entity' => $entity, 'table' => $table]));
        }

        // show footer at the end of each result list
        if (!isset($aliasedRequest['footers']) || is_array($footers = $aliasedRequest['footers'])
            && isset($footers[$table]) && $footers[$table] === true
        ) {
            print $view->render('AdminBundle:Admin:footer.html.php',                          array_merge($subVars, ['table' => $table]));
        } else if (is_array($footers = $aliasedRequest['footers'])
            && isset($footers[$table])
            && $view->exists('AdminBundle:Admin:footer-' . $footers[$table] . '.html.php')
        ) {
            print $view->render('AdminBundle:Admin:footer-' . $footers[$table] . '.html.php', array_merge($subVars, ['table' => $table]));
        }
    } ?>
</div>