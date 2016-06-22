<?php
use Admin\Bundle\Controller\AdminController;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;

/** @var GlobalVariables $app */

$context = !empty($context) ? $context : jQuery($this);
$resultOutput = $context->filter('.results');

$selected = $resultOutput->find('[class*="-row"].selected');

$resultOutput->children('.views, header, footer, .highlighted-link, [class*="-row"]:not(.edit), [class*="-row"]:not(.edit) + .expandable:not([class*="-row"])')->remove();

$subVars = [
    'request' => $request,
    'results' => $results
];

$isFresh = false;
if($resultOutput->length == 0) {
    $isFresh = true;
    $resultOutput = $context->append('<div class="results"></div>')->find('.results');
}
$resultOutput->data('request', $request)->attr('data-request', json_encode($request))
    ->addClass(isset($request['classes']) && is_array($request['classes'])
        ? implode(' ', $request['classes'])
        : '');

// update group listing with every results request
if(isset($resultsJSON)) {
    $resultOutput->data('allGroups', $resultsJSON['allGroups'])->attr('data-results', json_encode($resultsJSON['allGroups']));
}
// TODO: refresh data before show view?
//if($isFresh) {
//    print ($context->html());
//    return;
//}

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
    else { ?><div class="views"><ul><li><a href="#switch-view-" data-extend="{}">Refresh</a></li></div><?php }
    $view['slots']->stop();

    $views = $view['slots']->get('view-settings');
    //$resultOutput->prepend($views);
    //if(!empty($views)) {
    //    $last = $resultOutput->find('.views');
    //}
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
            $header = $view->render('AdminBundle:Admin:header.html.php',                                         array_merge($subVars, ['table' => $table]));
        } else if (is_array($headers = $aliasedRequest['headers'])
            && isset($headers[$table])
            && $view->exists(implode('', ['AdminBundle:Admin:header-' , $headers[$table] , '.html.php']))) {
            $header = $view->render(implode('', ['AdminBundle:Admin:header-' , $headers[$table] , '.html.php']), array_merge($subVars, ['table' => $table]));
        }

        if(!empty($header)) {
            if(empty($last) || $last->length == 0) {
                $resultOutput->prepend($header);
            }
            else {
                $last->after($header);
            }

            $last = $resultOutput->find('header')->last();
        }
    }

    if($resultOutput->find(implode('', ['.results-', $table , $ext]))->length > 0) {
        $last = $resultOutput->find(implode('', ['.results-', $table , $ext, ',.results-', $table , $ext , ' + .expandable:not([class*="-row"])']))->last();
    }

    // print out all result entities
    $classes = '';
    foreach ($results[implode('', [$table , $ext])] as $entity) {
        $row = null;
        if ($view->exists(implode('', ['AdminBundle:Admin:row-' , $table , '.html.php']))) {
            $rowVars = array_merge($subVars, [
                'classes' => $classes,
                'table' => $table,
                'context' => $context->find(implode('', ['.results-', $table , $ext])),
                'tableId' => implode('', [$table , $ext])]);
            $rowVars[$table] = $entity;
            $row = $view->render(implode('', ['AdminBundle:Admin:row-' , $table , '.html.php']),                 $rowVars);
        } else {
            $row = $view->render('AdminBundle:Admin:row.html.php', array_merge($subVars, [
                'classes' => $classes,
                'entity' => $entity,
                'table' => $table,
                'context' => $context->find(implode('', ['.results-', $table , $ext])),
                'tableId' => implode('', [$table , $ext])]));
        }
        // TODO: update new row IDs, no insert if(isset($entity->newId))
        if(!empty($row)) {
            if(empty($last) || $last->length == 0) {
                $resultOutput->prepend($row);
            }
            else {
                $last->after($row);
            }

            $last = $resultOutput->find(implode('', ['.results-', $table , $ext, ',.results-', $table , $ext , ' + .expandable:not([class*="-row"])']))->last();
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
                $newRow = $view->render(implode('', ['AdminBundle:Admin:row-' , $table , '.html.php']),          $rowVars);
            } else {
                $newRow = $view->render('AdminBundle:Admin:row.html.php', array_merge($subVars, [
                    'classes' => $classes,
                    'entity' => $entity,
                    'table' => $table,
                    'tableId' => implode('', [$table , $ext])]));
            }

            if(!empty($newRow)) {
                if(empty($last) || $last->length == 0) {
                    $resultOutput->prepend($newRow);
                }
                else {
                    $last->after($newRow);
                }

                $last = $resultOutput->find(implode('', ['.results-', $table , $ext, ',.results-', $table , $ext , ' + .expandable:not([class*="-row"])']))->last();
            }
        }
    }

    // show footer at the end of each result list
    $footer = null;
    if (!isset($aliasedRequest['footers']) || is_array($footers = $aliasedRequest['footers'])
        && isset($footers[$table]) && $footers[$table] === true) {
        $footer = $view->render('AdminBundle:Admin:footer.html.php',                                             array_merge($subVars, ['table' => $table]));
    } else if (is_array($footers = $aliasedRequest['footers'])
        && isset($footers[$table])
        && $view->exists(implode('', ['AdminBundle:Admin:footer-' , $footers[$table] , '.html.php']))) {
        $footer = $view->render(implode('', ['AdminBundle:Admin:footer-' , $footers[$table] , '.html.php']),     array_merge($subVars, ['table' => $table]));
    }

    if(!empty($footer)) {
        if(empty($last) || $last->length == 0) {
            $resultOutput->prepend($footer);
        }
        else {
            $last->after($footer);
        }

        $last = $resultOutput->find('.highlighted-link, footer')->last();
    }
}

print ($context->html());
