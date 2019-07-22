<?php

namespace App\ServiceProvider;

use function DI\get;
use function DI\autowire;
use Pho\Core\ServiceProviderInterface;
use DI\ContainerBuilder;
use App\Lib\Acl\AclProcessor;

class AclServiceProvider implements ServiceProviderInterface
{
    public function register(ContainerBuilder $builder, array $opts = []): void
    {
        $def = array_merge([
            'acl.yml' => '',
            AclProcessor::class => autowire()
                ->constructor(get('acl.yml')),
            'acl' => get(AclProcessor::class),
        ], $opts);

        $builder->addDefinitions($def);
    }
}