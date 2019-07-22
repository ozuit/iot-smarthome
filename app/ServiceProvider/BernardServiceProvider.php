<?php

namespace App\ServiceProvider;

use function DI\get;
use function DI\autowire;
use Pho\Core\ServiceProviderInterface;
use DI\ContainerBuilder;
use Bernard\Serializer\SimpleSerializer;
use Bernard\QueueFactory\PersistentFactory;
use Bernard\Driver\PhpRedisDriver;
use Bernard\Driver\PredisDriver;
use Bernard\Router\SimpleRouter;
use Bernard\Producer;
use Bernard\Consumer;
use Bernard\QueueFactory;
use Redis;
use Predis\Client;

class BernardServiceProvider implements ServiceProviderInterface
{
    public function register(ContainerBuilder $builder, array $opts = []): void
    {
        $def = [
            'bernard.redis.host'    => '127.0.0.1',
            'bernard.redis.port'    => '6379',
            'bernard.redis.prefix'  => 'bernard:',
            'bernard.receivers'     => [],
            QueueFactory::class     => get(PersistentFactory::class),
        ];

        if (class_exists('Redis')) {
            $def[PhpRedisDriver::class] = autowire()
                ->constructor(get('bernard.redis'));
            $def['bernard.driver'] = get(PhpRedisDriver::class);
        } else {
            $def[PredisDriver::class] = autowire()
                ->constructor(get('bernard.predis'));
            $def['bernard.driver'] = get(PredisDriver::class);
        }

        $def[PersistentFactory::class] = autowire()
            ->constructor(
                get('bernard.driver'),
                get(SimpleSerializer::class)
            );

        $def[Producer::class] = autowire()
            ->constructor(get(PersistentFactory::class));

        $def[Consumer::class] = autowire()
            ->constructor(get('bernard.router'));

        $def['bernard.router'] = function($c) {
            $router = new SimpleRouter($c->get('bernard.receivers'));

            if ($c->has('bernard.router.callback')) {
                $callback = $c->get('bernard.router.callback');
                $callback($router);
            };

            return $router;
        };

        $def['bernard.redis'] = function ($c) {
            $redis = new Redis();
            $redis->connect($c->get('bernard.redis.host'), intval($c->get('bernard.redis.port')));
            $redis->setOption(Redis::OPT_PREFIX, $c->get('bernard.redis.prefix'));

            return $redis;
        };

        $def['bernard.predis'] = function ($c) {
            $predis = new Client([
                'scheme' => 'tcp',
                'host' => $c->get('bernard.redis.host'),
                'port' => intval($c->get('bernard.redis.port')),
            ], [
                'prefix' => $c->get('bernard.redis.prefix'),
            ]);

            return $predis;
        };

        $def['bernard.producer'] = get(Producer::class);

        $def['bernard.consumer'] = get(Consumer::class);

        $def = array_merge($def, $opts);

        $builder->addDefinitions($def);
    }
}
