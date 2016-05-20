<?php


$container->loadFromExtension('assetic', [
    'assets' => [
        'results_css' => [
            'inputs' => [
                '@AdminBundle/Resources/public/css/results.css',
                '@AdminBundle/Resources/public/css/results-edit.css',
                '@AdminBundle/Resources/public/css/results-expand.css',
                '@AdminBundle/Resources/public/css/results-tiles.css'
            ],
            'filters' => [],
            'options' => [
                'output' => 'bundles/admin/css/*.css',
            ],
        ],
        'layout_css' => [
            'inputs' => [
                '@StudySauceBundle/Resources/public/css/jquery-ui.min.css',
                '@StudySauceBundle/Resources/public/css/normalize.css',
                '@StudySauceBundle/Resources/public/css/selectize.default.css',
                '@StudySauceBundle/Resources/public/js/datetimepicker-master/jquery.datetimepicker.css',
                '@StudySauceBundle/Resources/public/css/fonts.css',
                '@StudySauceBundle/Resources/public/css/sauce.css',
                '@StudySauceBundle/Resources/public/css/dialog.css',
                '@results_css',
            ],
            'filters' => [],
            'options' => [
                'output' => 'bundles/studysauce/css/*.css',
            ],
        ],
        'funnel' => [
            'inputs' => [
                '@StudySauceBundle/Resources/public/js/moment.min.js',
                '@StudySauceBundle/Resources/public/js/moment.phpDateFormat.js',
                '@StudySauceBundle/Resources/public/js/selectize.min.js',
                '@StudySauceBundle/Resources/public/js/jquery.plugin.js',
                '@StudySauceBundle/Resources/public/js/jquery.timeentry.js',
                '@StudySauceBundle/Resources/public/js/datetimepicker-master/build/jquery.datetimepicker.full.min.js',
                '@StudySauceBundle/Resources/public/js/jquery.scrollintoview.js',
                '@StudySauceBundle/Resources/public/js/keymaster.js',
                '@StudySauceBundle/Resources/public/js/papaparse.min.js',
                '@StudySauceBundle/Resources/public/js/sauce.js',
                '@StudySauceBundle/Resources/public/js/dashboard-dialogs.js',
                '@StudySauceBundle/Resources/public/js/contact.js',
            ],
            'filters' => [],
            'options' => [
                'output' => 'bundles/studysauce/js/*.js',
            ],
        ],
        'dashboard_scripts' => [
            'inputs' => [
                '@funnel',
                '@StudySauceBundle/Resources/public/js/jquery.jplayer.min.js',
                '@StudySauceBundle/Resources/public/js/plupload/js/plupload.full.min.js',
                //'@StudySauceBundle/Resources/public/js/plupload/js/moxie.js',
                //'@StudySauceBundle/Resources/public/js/plupload/js/plupload.dev.js',
                '@StudySauceBundle/Resources/public/js/dashboard.js',
                '@StudySauceBundle/Resources/public/js/dashboard-search.js',
                '@StudySauceBundle/Resources/public/js/dashboard-publish.js',
                '@StudySauceBundle/Resources/public/js/dashboard-player.js',
                '@StudySauceBundle/Resources/public/js/dashboard-upload.js',
                '@AdminBundle/Resources/public/js/results.js'
            ],
            'filters' => [],
            'options' => [
                'output' => 'bundles/studysauce/js/*.js',
            ],
        ],
        'landing_scripts' => [
            'inputs' => [
                '@StudySauceBundle/Resources/public/js/jquery.scrollintoview.js',
                '@StudySauceBundle/Resources/public/js/landing.js',
                '@StudySauceBundle/Resources/public/js/sauce.js',
                '@StudySauceBundle/Resources/public/js/dashboard-dialogs.js',
                '@StudySauceBundle/Resources/public/js/contact.js'
            ],
            'filters' => [],
            'options' => [
                'output' => 'bundles/studysauce/js/*.js',
            ],
        ],
        'layout' => [
            'inputs' => [
                '@StudySauceBundle/Resources/public/js/jquery-2.2.3.min.js',
                '@StudySauceBundle/Resources/public/js/jquery.textfill.min.js',
                '@StudySauceBundle/Resources/public/js/jquery-ui.min.js',
                '@StudySauceBundle/Resources/public/js/underscore-min.js'
            ],
            'filters' => [],
            'options' => [
                'output' => 'bundles/studysauce/js/*.js',
            ],
        ],
    ],
]);




