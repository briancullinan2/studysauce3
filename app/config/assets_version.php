<?php
// app/config/assets_version.php
$container->loadFromExtension('framework', array(
    'templating'      => array(
        'assets_version' => exec('git rev-parse --short HEAD'),
    ),
));