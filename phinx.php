<?php
require_once __DIR__ . '/bootstrap/helpers.php';
$autoloader = (require_once __DIR__ . '/vendor/autoload.php');

$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();


$dsn = sprintf('%s:dbname=%s;host=%s;charset=%s', env('DB_DRIVER', 'mysql'), env('DB_NAME'),
    env('DB_HOST', '127.0.0.1'), env('DB_CHARSET', 'utf8mb4'));
$pdo = new PDO($dsn, env('DB_USER'), env('DB_PASSWORD'), []);

return [
    'paths' => [
        'migrations' => __DIR__ . '/db/migrations',
        'seeds' => __DIR__ . '/db/seeds',
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_database' => 'default',
        'default' => [
            'name' => env('DB_NAME'),
            'connection' => $pdo,
        ],
    ],
];
