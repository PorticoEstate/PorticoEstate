<?php

use Symfony\Component\HttpFoundation\Request;

require __DIR__ . '/../plugin/vendor/autoload.php';
require_once __DIR__ . '/../plugin/app/AppKernel.php';
if (PHP_VERSION_ID < 70000) {
    include_once __DIR__ . '/../plugin/var/bootstrap.php.cache';
}

$kernel = new AppKernel('prod', false);
if (PHP_VERSION_ID < 70000) {
    $kernel->loadClassCache();
}
//$kernel = new AppCache($kernel);

// When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
//Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
