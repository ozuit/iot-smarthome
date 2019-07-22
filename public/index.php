<?php

use Pho\Http\HttpProgram;

require_once dirname(__DIR__).'/bootstrap/helpers.php';
require_once dirname(__DIR__).'/vendor/autoload.php';
require_once dirname(__DIR__).'/bootstrap/app.php';
require_once dirname(__DIR__).'/bootstrap/load.php';
require_once dirname(__DIR__).'/bootstrap/http.php';


if (!env('DEBUG')) {
    $client = new Raven_Client(env('SENTRY_URL'));
    $error_handler = new Raven_ErrorHandler($client);
    $error_handler->registerExceptionHandler();
    $error_handler->registerErrorHandler();
    $error_handler->registerShutdownFunction();
}

$pho_container = $app->buildContainer();
$app->run(HttpProgram::class);
