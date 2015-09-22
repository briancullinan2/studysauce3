<?php

// app/config/routing.php
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

$collection->add(
    'command_control',
    new Route(
        '/cerebro/{_format}',
        ['_controller' => 'AdminBundle:Admin:index', '_format' => 'adviser'],
        ['_format' => DASHBOARD_VIEWS]
    )
);

$collection->add(
    'command_callback',
    new Route(
        '/cerebro/list',
        ['_controller' => 'AdminBundle:Admin:index', '_format' => 'tab'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add(
    'activity',
    new Route(
        '/activity/{_format}',
        ['_controller' => 'AdminBundle:Activity:index', '_format' => 'adviser'],
        ['_format' => DASHBOARD_VIEWS],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest() || !request.isXmlHttpRequest()'
    )
);

$collection->add(
    'results',
    new Route(
        '/results/{_format}',
        ['_controller' => 'AdminBundle:Results:index', '_format' => 'adviser'],
        ['_format' => DASHBOARD_VIEWS],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest() || !request.isXmlHttpRequest()'
    )
);

$collection->add(
    'results_callback',
    new Route(
        '/results/list',
        ['_controller' => 'AdminBundle:Results:index', '_format' => 'tab'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add(
    'results_user',
    new Route(
        '/results/user',
        ['_controller' => 'AdminBundle:Results:user', '_format' => 'tab'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add(
    'remove_user',
    new Route(
        '/cerebro/remove/user',
        ['_controller' => 'AdminBundle:Admin:removeUser', '_format' => 'tab'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add(
    'cancel_user',
    new Route(
        '/cerebro/cancel/user',
        ['_controller' => 'AdminBundle:Admin:cancelUser', '_format' => 'tab'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add(
    'save_user',
    new Route(
        '/cerebro/save/user',
        ['_controller' => 'AdminBundle:Admin:saveUser', '_format' => 'tab'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add(
    'add_user',
    new Route(
        '/cerebro/add/user',
        ['_controller' => 'AdminBundle:Admin:addUser', '_format' => 'tab'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add(
    'save_group',
    new Route(
        '/cerebro/save/group',
        ['_controller' => 'AdminBundle:Admin:saveGroup', '_format' => 'tab'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add(
    'reset_user',
    new Route(
        '/cerebro/reset/user',
        ['_controller' => 'AdminBundle:Admin:resetUser', '_format' => 'tab'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add(
    'validation',
    new Route(
        '/validation/{_format}',
        ['_controller' => 'AdminBundle:Validation:index', '_format' => 'adviser'],
        ['_format' => DASHBOARD_VIEWS]
    )
);

$collection->add(
    'validation_test',
    new Route(
        '/validation/test',
        ['_controller' => 'AdminBundle:Validation:test'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add(
    'validation_result',
    new Route(
        '/validation/result',
        ['_controller' => 'AdminBundle:Validation:result'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add(
    'validation_refresh',
    new Route(
        '/validation/refresh',
        ['_controller' => 'AdminBundle:Validation:refresh'],
        [],
        [],
        '',
        [],
        [],
        'true || request.isXmlHttpRequest()'
    )
);

$collection->add(
    'emails',
    new Route(
        '/emails/{_format}',
        ['_controller' => 'AdminBundle:Emails:index', '_format' => 'adviser'],
        ['_format' => DASHBOARD_VIEWS]
    )
);

$collection->add(
    'emails_callback',
    new Route(
        '/emails/list',
        ['_controller' => 'AdminBundle:Emails:index', '_format' => 'tab'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add(
    'emails_template',
    new Route(
        '/emails/template/{_email}',
        ['_controller' => 'AdminBundle:Emails:template', '_email' => ''],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest() || !request.isXmlHttpRequest()'
    )
);

$collection->add(
    'emails_send',
    new Route(
        '/emails/send/{_email}',
        ['_controller' => 'AdminBundle:Emails:send', '_email' => ''],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest() || !request.isXmlHttpRequest()'
    )
);

$collection->add(
    'emails_search',
    new Route(
        '/emails/search',
        ['_controller' => 'AdminBundle:Emails:search'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add(
    'emails_save',
    new Route(
        '/emails/save',
        ['_controller' => 'AdminBundle:Emails:save'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);
$collection->add(
    'import',
    new Route(
        '/import/{_format}',
        ['_controller' => 'AdminBundle:Import:index', '_format' => 'adviser'],
        ['_format' => DASHBOARD_VIEWS]
    )
);
$collection->add(
    'import_save',
    new Route(
        '/import/save',
        ['_controller' => 'AdminBundle:Import:update'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);
$collection->add(
    'userlist',
    new Route(
        '/userlist/{_format}',
        ['_controller' => 'AdminBundle:Adviser:userlist', '_format' => 'adviser'],
        ['_format' => DASHBOARD_VIEWS]
    )
);
$collection->add(
    'userlist_status',
    new Route(
        '/userlist/status',
        ['_controller' => 'AdminBundle:Adviser:updateStatus'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);
$collection->add(
    'adviser',
    new Route(
        '/adviser/{_user}/{_tab}/{_format}',
        ['_controller' => 'AdminBundle:Adviser:adviser', '_format' => 'adviser'],
        ['_format' => DASHBOARD_VIEWS, '_user' => '[0-9]+']
    )
);
$collection->add(
    'adviser_partner',
    new Route(
        '/partner/{_user}/{_tab}/{_format}',
        ['_controller' => 'AdminBundle:Adviser:partner', '_format' => 'adviser'],
        ['_format' => DASHBOARD_VIEWS, '_user' => '[0-9]+']
    )
);
return $collection;