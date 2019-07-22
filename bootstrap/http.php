<?php

use function DI\autowire;
use Pho\ServiceProvider\HttpServiceProvider;
use Pho\ServiceProvider\SessionServiceProvider;
use App\ServiceProvider\AclServiceProvider as AppAclServiceProvider;

$app->register(new HttpServiceProvider(), [
    'kernel.class' => App\Http\Kernel::class,
    Pho\Routing\RouteLoader::class => autowire(App\Http\Router::class),
]);
$app->register(new SessionServiceProvider());
$app->register(new AppAclServiceProvider(), [
    'acl.yml' => ROOT_PATH . DS . 'public' . DS . 'acl.yml',
]);
