<?php

use function DI\get;
use Pho\ServiceProvider\ConsoleServiceProvider;
use App\Console\ConsumeKernel;
use App\ServiceProvider\SenderServiceProvider;
use App\ServiceProvider\MailServiceProvider;

$app->register(new ConsoleServiceProvider(), [
    'kernel.class' => ConsumeKernel::class,
    'debug' => get('env.debug'),
    'logging' => get('env.logging'),
]);
$app->register(new MailServiceProvider(), [
    'swiftmailer.type' => env('MAIL_TYPE'),
    'swiftmailer.options' => [
        'host' => env('MAIL_SMTP_HOSTNAME'),
        'port' => env('MAIL_SMTP_PORT'),
        'username' => env('MAIL_SMTP_USERNAME'),
        'password' => env('MAIL_SMTP_PASSWORD'),
        'encryption' => env('MAIL_SMTP_ENCRYPTION'),
        'auth_mode' => env('MAIL_SMTP_AUTH_MODE'),
        'access_key' => env('MAIL_SES_ACCESS_KEY'),
        'secret_key' => env('MAIL_SES_SECRET_KEY'),
    ],
]);
$app->register(new SenderServiceProvider(), [
    'sender.logger.path' => storage_path('log'.DS.'sender.log'),
]);
