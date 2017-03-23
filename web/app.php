<?php

use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\HttpFoundation\Request;

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';

// Use APC for autoloading to improve performance.
// Change 'sf2' to a unique prefix in order to prevent cache key conflicts
// with other applications also using APC.
/*
$apcLoader = new ApcClassLoader('sf2', $loader);
$loader->unregister();
$apcLoader->register(true);
*/

require_once __DIR__.'/../app/AppKernel.php';
//require_once __DIR__.'/../app/AppCache.php';

if (isset($_SERVER) &&
    is_array($_SERVER) &&
    isset($_SERVER['HTTP_HOST'])) {
    if(preg_match('/test\.studysauce\.com/', $_SERVER['HTTP_HOST'])) {
        Symfony\Component\Debug\Debug::enable();
        $kernel = new AppKernel('test', true);
    }
    elseif(preg_match('/staging\.studysauce\.com/', $_SERVER['HTTP_HOST'])
        || preg_match('/localhost/', $_SERVER['HTTP_HOST'])) {
        Symfony\Component\Debug\Debug::enable();
        $kernel = new AppKernel('dev', true);
    }
    elseif(preg_match('/www\.studysauce\.com/', $_SERVER['HTTP_HOST'])
        || preg_match('/cerebro\.studysauce\.com/', $_SERVER['HTTP_HOST'])
        || preg_match('/^studysauce\.com/', $_SERVER['HTTP_HOST'])) {
        $kernel = new AppKernel('prod', false);
    }
}
else {
    die('no http host');
}
$kernel->loadClassCache();
//$kernel = new AppCache($kernel);

// When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
//Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
