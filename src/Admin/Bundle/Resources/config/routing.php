<?php

// app/config/routing.php
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

$collection->add(
    'command',
    new Route(
        '/command/{_format}',
        ['_controller' => 'AdminBundle:Admin:index', '_format' => 'index'],
        ['_format' => DASHBOARD_VIEWS]
    )
);

$collection->add(
    'command_callback',
    new Route(
        '/command/list',
        ['_controller' => 'AdminBundle:Admin:index', '_format' => 'tab']
    )
);

$collection->add(
    'activity',
    new Route(
        '/activity/{_format}',
        ['_controller' => 'AdminBundle:Activity:index', '_format' => 'index'],
        ['_format' => DASHBOARD_VIEWS]
    )
);

$collection->add(
    'results',
    new Route(
        '/results/{_format}',
        ['_controller' => 'AdminBundle:Results:index', '_format' => 'index'],
        ['_format' => DASHBOARD_VIEWS]
    )
);

$collection->add(
    'results_callback',
    new Route(
        '/results/list',
        ['_controller' => 'AdminBundle:Results:index', '_format' => 'tab']
    )
);

$collection->add(
    'results_user',
    new Route(
        '/results/user',
        ['_controller' => 'AdminBundle:Results:user', '_format' => 'tab']
    )
);

$collection->add(
    'remove_user',
    new Route(
        '/command/remove/user',
        ['_controller' => 'AdminBundle:Admin:removeUser', '_format' => 'tab']
    )
);

$collection->add(
    'cancel_user',
    new Route(
        '/command/cancel/user',
        ['_controller' => 'AdminBundle:Admin:cancelUser', '_format' => 'tab']
    )
);

$collection->add(
    'save_user',
    new Route(
        '/command/save/user',
        ['_controller' => 'AdminBundle:Admin:saveUser', '_format' => 'tab']
    )
);

$collection->add(
    'add_user',
    new Route(
        '/command/add/user',
        ['_controller' => 'AdminBundle:Admin:addUser', '_format' => 'tab']
    )
);

$collection->add(
    'save_group',
    new Route(
        '/command/save/group',
        ['_controller' => 'AdminBundle:Admin:saveGroup', '_format' => 'tab']
    )
);

$collection->add(
    'reset_user',
    new Route(
        '/command/reset/user',
        ['_controller' => 'AdminBundle:Admin:resetUser', '_format' => 'tab']
    )
);

$collection->add(
    'validation',
    new Route(
        '/validation/{_format}',
        ['_controller' => 'AdminBundle:Validation:index', '_format' => 'index'],
        ['_format' => DASHBOARD_VIEWS]
    )
);

$collection->add(
    'validation_test',
    new Route(
        '/validation/test',
        ['_controller' => 'AdminBundle:Validation:test']
    )
);

$collection->add(
    'validation_result',
    new Route(
        '/validation/result',
        ['_controller' => 'AdminBundle:Validation:result']
    )
);

$collection->add(
    'validation_refresh',
    new Route(
        '/validation/refresh',
        ['_controller' => 'AdminBundle:Validation:refresh']
    )
);

$collection->add(
    'emails',
    new Route(
        '/emails/{_format}',
        ['_controller' => 'AdminBundle:Emails:index', '_format' => 'index'],
        ['_format' => DASHBOARD_VIEWS]
    )
);

$collection->add(
    'emails_callback',
    new Route(
        '/emails/list',
        ['_controller' => 'AdminBundle:Emails:index', '_format' => 'tab']
    )
);

$collection->add(
    'emails_template',
    new Route(
        '/emails/template/{_email}',
        ['_controller' => 'AdminBundle:Emails:template', '_email' => '']
    )
);

$collection->add(
    'emails_send',
    new Route(
        '/emails/send/{_email}',
        ['_controller' => 'AdminBundle:Emails:send', '_email' => '']
    )
);

$collection->add(
    'emails_search',
    new Route(
        '/emails/search',
        ['_controller' => 'AdminBundle:Emails:search']
    )
);

$collection->add(
    'emails_save',
    new Route(
        '/emails/save',
        ['_controller' => 'AdminBundle:Emails:save']
    )
);
$collection->add(
    'import',
    new Route(
        '/import/{_format}',
        ['_controller' => 'AdminBundle:Import:index', '_format' => 'index'],
        ['_format' => DASHBOARD_VIEWS]
    )
);
$collection->add(
    'import_save',
    new Route(
        '/import/save',
        ['_controller' => 'AdminBundle:Import:update']
    )
);
$collection->add(
    'userlist',
    new Route(
        '/userlist/{_format}',
        ['_controller' => 'AdminBundle:Adviser:userlist', '_format' => 'index'],
        ['_format' => DASHBOARD_VIEWS]
    )
);
$collection->add(
    'userlist_status',
    new Route(
        '/userlist/status',
        ['_controller' => 'AdminBundle:Adviser:updateStatus']
    )
);
$collection->add(
    'adviser',
    new Route(
        '/adviser/{_user}/{_tab}/{_format}',
        ['_controller' => 'AdminBundle:Adviser:adviser', '_format' => 'index'],
        ['_format' => DASHBOARD_VIEWS, '_user' => '[0-9]+']
    )
);
$collection->add(
    'adviser_partner',
    new Route(
        '/partner/{_user}/{_tab}/{_format}',
        ['_controller' => 'AdminBundle:Adviser:partner', '_format' => 'index'],
        ['_format' => DASHBOARD_VIEWS, '_user' => '[0-9]+']
    )
);
return $collection;