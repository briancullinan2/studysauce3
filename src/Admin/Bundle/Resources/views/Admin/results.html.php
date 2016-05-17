<?php
use Admin\Bundle\Controller\AdminController;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;

/** @var GlobalVariables $app */

$context = !empty($context) ? $context : jQuery($this);
$resultOutput = $context->filter('.results');

$selected = $resultOutput->find('[class*="-row"].selected');

$resultOutput->children('.view, .template, .template + .expandable:not([class*="-row"]), header, footer, .highlighted-link, [class*="-row"]:not(.edit), [class*="-row"]:not(.edit) + .expandable:not([class*="-row"])')->remove();

$subVars = [
    'request' => $request,
    'results' => $results
];

if($resultOutput->length == 0) {
    $resultOutput = $context->append('<div class="results"></div>')->find('.results');
}
$resultOutput->data('request', $request)->attr('data-request', json_encode($request))
    ->addClass(isset($request['classes']) && is_array($request['classes'])
        ? implode(' ', $request['classes'])
        : '');


// TODO: bring back search header for list format
//if (!isset($request['headers'])) {
//    print ($view->render('AdminBundle:Admin:header-search.html.php', array_merge($subVars, ['tables' => $tables])));
//}

if($app->getUser()->getEmailCanonical() == 'brian@studysauce.com') {
    $view['slots']->start('view-settings');
    if(!empty($request['views'])) { ?><div class="views"><ul><?php
        foreach($request['views'] as $v => $extend) {
            ?><li><a href="#switch-view-<?php print ($v); ?>"><?php print ($v); ?></a></li><?php
        } ?></ul></div><?php
    }
    else { ?>
        <div class="views"><ul><li><a href="#switch-view-" data-extend="{}">Refresh</a></li></div>
    <?php }
    $view['slots']->stop();

    $resultOutput->prepend($last = jQuery($view['slots']->get('view-settings'))->find('.views'));
}

foreach ($tables as $table => $t) {
    $tableParts = explode('-', $table);
    $ext = implode('-', array_slice($tableParts, 1));
    $table = explode('-', $table)[0];
    $aliasedRequest = (array)(new stdClass());
    if(strlen($ext) > 0) {
        $aliasedRequest['tables'] = (array)(new stdClass());
        $ext = implode('', ['-' , $ext]);
        $aliasLen = strlen($table) + strlen($ext);
        foreach ($request as $r => $s) {
            if (substr($r, 0, $aliasLen) == implode('', [$table , $ext])) {
                $aliasedRequest[substr($r, $aliasLen)] = $s;
            }
        }
        $aliasedRequest['tables'][$table] = $request['tables'][implode('', [$table , $ext])];
    }
    $aliasedRequest = array_merge($request, $aliasedRequest);
    $subVars = array_merge($subVars, ['request' => $aliasedRequest, 'tables' => $aliasedRequest['tables']]);

    $isNew = isset($aliasedRequest['new']) && ($aliasedRequest['new'] === true
            || is_array($aliasedRequest['new']) && in_array($table, $aliasedRequest['new']));

    // show header template
    if (count($results[implode('', [$table , $ext])]) > 0 || $isNew) {
        $header = null;
        if (!isset($aliasedRequest['headers']) || is_array($headers = $aliasedRequest['headers'])
            && isset($headers[$table]) && $headers[$table] === true) {
            $header = jQuery($view->render('AdminBundle:Admin:header.html.php',                                         array_merge($subVars, ['table' => $table])));
        } else if (is_array($headers = $aliasedRequest['headers'])
            && isset($headers[$table])
            && $view->exists(implode('', ['AdminBundle:Admin:header-' , $headers[$table] , '.html.php']))) {
            $header = jQuery($view->render(implode('', ['AdminBundle:Admin:header-' , $headers[$table] , '.html.php']), array_merge($subVars, ['table' => $table])));
        }

        if(empty($last) || $last->length == 0) {
            $resultOutput->prepend($header);
        }
        else {
            $last->after($header);
        }

        if(!empty($header) && $header->length > 0) {
            $last = $header->last();
        }
    }

    if($resultOutput->find(implode('', ['.results-', $table , $ext]))->length > 0) {
        $last = $resultOutput->find(implode('', ['.results-', $table , $ext]))->last();
    }

    // print out all result entities
    $classes = '';
    foreach ($results[implode('', [$table , $ext])] as $entity) {
        $row = null;
        if ($view->exists(implode('', ['AdminBundle:Admin:row-' , $table , '.html.php']))) {
            $rowVars = array_merge($subVars, [
                'classes' => $classes,
                'table' => $table,
                'context' => $context,
                'tableId' => implode('', [$table , $ext])]);
            $rowVars[$table] = $entity;
            $row = jQuery($view->render(implode('', ['AdminBundle:Admin:row-' , $table , '.html.php']),                 $rowVars));
        } else {
            $row = jQuery($view->render('AdminBundle:Admin:row.html.php', array_merge($subVars, [
                'classes' => $classes,
                'entity' => $entity,
                'table' => $table,
                'context' => $context,
                'tableId' => implode('', [$table , $ext])])));
        }
        // TODO: update new row IDs, no insert if(isset($entity->newId))
        if(empty($last) || $last->length == 0) {
            $resultOutput->prepend($row);
        }
        else {
            $last->after($row);
        }

        if(!empty($row) && $row->length > 0) {
            $last = $row->last();
        }
    }

    // print out new rows with blank objects
    if ($isNew) {
        $entity = AdminController::createEntity($table);
        $classes = ' empty';
        $newCount = !empty(intval($aliasedRequest[implode('', ['count-' , $table])]))
            ? intval($aliasedRequest[implode('', ['count-' , $table])])
            : 1;
        for ($nc = 0; $nc < $newCount; $nc++) {
            $newRow = null;
            if ($view->exists(implode('', ['AdminBundle:Admin:row-' , $table , '.html.php']))) {
                $rowVars = array_merge($subVars, [
                    'classes' => $classes,
                    'table' => $table,
                    'tableId' => implode('', [$table , $ext])]);
                $rowVars[$table] = $entity;
                $newRow = jQuery($view->render(implode('', ['AdminBundle:Admin:row-' , $table , '.html.php']),          $rowVars));
            } else {
                $newRow = jQuery($view->render('AdminBundle:Admin:row.html.php', array_merge($subVars, [
                    'classes' => $classes,
                    'entity' => $entity,
                    'table' => $table,
                    'tableId' => implode('', [$table , $ext])])));
            }
            if(empty($last) || $last->length == 0) {
                $resultOutput->prepend($newRow);
            }
            else {
                $last->after($newRow);
            }

            if(!empty($newRow) && $newRow->length > 0) {
                $last = $newRow->last();
            }
        }
    }

    // show footer at the end of each result list
    $footer = null;
    if (!isset($aliasedRequest['footers']) || is_array($footers = $aliasedRequest['footers'])
        && isset($footers[$table]) && $footers[$table] === true) {
        $footer = jQuery($view->render('AdminBundle:Admin:footer.html.php',                                             array_merge($subVars, ['table' => $table])));
    } else if (is_array($footers = $aliasedRequest['footers'])
        && isset($footers[$table])
        && $view->exists(implode('', ['AdminBundle:Admin:footer-' , $footers[$table] , '.html.php']))) {
        $footer = jQuery($view->render(implode('', ['AdminBundle:Admin:footer-' , $footers[$table] , '.html.php']),     array_merge($subVars, ['table' => $table])));
    }

    if(empty($last) || $last->length == 0) {
        $resultOutput->prepend($footer);
    }
    else {
        $last->after($footer);
    }

    if(!empty($footer) && $footer->length > 0) {
        $last = $footer->last();
    }
}

print ($context->html());
