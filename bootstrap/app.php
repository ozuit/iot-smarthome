<?php
use Pho\Core\Application;
use Pho\Core\ContainerBuilderFactory;

date_default_timezone_set("Asia/Ho_Chi_Minh");

if (env('APP_ENV', 'dev') == 'dev') {
    $containerBuilder = ContainerBuilderFactory::development();
} else {
    $containerBuilder = ContainerBuilderFactory::production(true, false, storage_path('cache'), storage_path('cache'));
}

$app = new Application($containerBuilder);
