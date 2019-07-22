<?php

namespace App\ServiceProvider;

use function DI\get;
use function DI\autowire;
use DI\ContainerBuilder;
use App\Lib\Sender\Mailer;
use App\Lib\Sender\MailerNull;
use App\Lib\Sender\MailerProduction;
use App\Lib\Sender\SenderLogger;
use Pho\Core\ServiceProviderInterface;

class SenderServiceProvider implements ServiceProviderInterface
{
    public function register(ContainerBuilder $builder, array $opts = []): void
    {
        $def = array_merge($opts, [
            'mail.production' => function () {
                return env('APP_ENV', 'dev') === 'production' || env('TEST_SENDER_EMAIL') === true;
            },
            Mailer::class => function ($c) {
                if ($c->get('mail.production')) {
                    return $c->get(MailerProduction::class);
                }
                return $c->get(MailerNull::class);
            },
            'pn.mailer' => get(Mailer::class),
            SenderLogger::class => autowire()
                ->constructor(get('sender.logger.path')),
            'sender_logger' => get(SenderLogger::class),
        ]);

        $builder->addDefinitions($def);
    }
}