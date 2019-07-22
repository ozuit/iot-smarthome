<?php

use App\Lib\Consumer\Api;
use App\ServiceProvider\BernardServiceProvider;

$dotenv = Dotenv\Dotenv::create(dirname(__DIR__));
$dotenv->load();

// Pho
$app->register(new Pho\ServiceProvider\PhoServiceProvider(), [
    'DEBUG' => env('DEBUG', false),
]);

// Log
$app->register(new Pho\ServiceProvider\LogServiceProvider(), [
    'logger.stream' => storage_path('log/pho.log'),
]);

// Twig
$app->register(new Pho\ServiceProvider\TwigServiceProvider(), [
    'twig.path' => resources_path('views'),
    'twig.options' => [
        'cache' => env('TWIG_CACHE', false) ? storage_path(env('TWIG_CACHE')) : false,
    ]
]);

// Redis
if (class_exists('Redis')) {
    $app->register(new Pho\ServiceProvider\RedisServiceProvider());
} else {
    $app->register(new Pho\ServiceProvider\PredisServiceProvider());
}

// Eloquent
$app->register(new Pho\ServiceProvider\EloquentServiceProvider(), [
    'db.connection' => [
        'driver' => env('DB_DRIVER', 'mysql'),
        'host' => env('DB_HOST', '127.0.0.1'),
        'database' => env('DB_NAME', 'test'),
        'username' => env('DB_USER', 'root'),
        'password' => env('DB_PASSWORD', ''),
        'charset' => env('DB_CHARSET', 'utf8mb4'),
        'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
        'prefix' => env('DB_PREFIX', null),
    ],
]);
$app->register(new BernardServiceProvider(), [
    'bernard.redis.host' => env('BERNARD_REDIS_HOST'),
    'bernard.redis.port' => env('BERNARD_REDIS_PORT', 6379),
    'bernard.redis.prefix' => env('BERNARD_REDIS_PREFIX', 'bernard:'),
    'bernard.router.callback' => function($container) {
        return function($router) use ($container) {
            $router->add('Api', $container->get(Api::class));
        };
    },
]);
