<?php

namespace App\ServiceProvider;

use function DI\get;
use function DI\autowire;
use DI\ContainerBuilder;
use Pho\Core\ServiceProviderInterface;
use Swift_Mailer;
use Swift_AWSTransport;
use Swift_Transport_EsmtpTransport;
use Swift_Transport_StreamBuffer;
use Swift_StreamFilters_StringReplacementFilterFactory;
use Swift_Transport_Esmtp_AuthHandler;
use Swift_Transport_Esmtp_Auth_CramMd5Authenticator;
use Swift_Transport_Esmtp_Auth_LoginAuthenticator;
use Swift_Transport_Esmtp_Auth_PlainAuthenticator;
use Swift_Events_SimpleEventDispatcher;

class MailServiceProvider implements ServiceProviderInterface
{
    public function register(ContainerBuilder $builder, array $opts = []): void
    {
        $def = array_merge($opts, [
            'swiftmailer.transport' => function ($c) {
                $options = array_replace([
                    'host' => 'localhost',
                    'port' => 25,
                    'username' => '',
                    'password' => '',
                    'encryption' => null,
                    'auth_mode' => null,
                    'stream_context_options' => [],
                    'access_key' => '',
                    'secret_key' => '',
                ], $c->get('swiftmailer.options'));
                if ($c->get('swiftmailer.type') === 'SES') {
                    $transport = new Swift_AWSTransport($options['access_key'], $options['secret_key']);
                } else {
                    $buffer = new Swift_Transport_StreamBuffer(new Swift_StreamFilters_StringReplacementFilterFactory());
                    $authhandler = new Swift_Transport_Esmtp_AuthHandler([
                        new Swift_Transport_Esmtp_Auth_CramMd5Authenticator(),
                        new Swift_Transport_Esmtp_Auth_LoginAuthenticator(),
                        new Swift_Transport_Esmtp_Auth_PlainAuthenticator(),
                    ]);
                    $eventdispatcher = new Swift_Events_SimpleEventDispatcher();
                    $transport = new Swift_Transport_EsmtpTransport($buffer, [$authhandler], $eventdispatcher);
                    $transport->setHost($options['host']);
                    $transport->setPort($options['port']);
                    $transport->setEncryption($options['encryption']);
                    $transport->setUsername($options['username']);
                    $transport->setPassword($options['password']);
                    $transport->setAuthMode($options['auth_mode']);
                    $transport->setStreamOptions($options['stream_context_options']);
                }
                return $transport;
            },
            Swift_Mailer::class => autowire()->constructor(get('swiftmailer.transport')),
        ]);

        $builder->addDefinitions($def);
    }
}