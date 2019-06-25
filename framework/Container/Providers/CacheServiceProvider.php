<?php

namespace Amber\Framework\Container\Providers;

use Amber\Cache\Cache;
use Amber\Cache\Driver\SimpleCache;
use Psr\SimpleCache\CacheInterface;

class CacheServiceProvider extends ServiceProvider
{
    public function setUp(): void
    {
        $container = static::getContainer();

        $container->register(CacheInterface::class, Cache::class)
            ->afterConstruct(
                'pushHandler',
                function () {
                    $driver = config('cache.default.driver');
                    return new $driver(config('cache.default.path'));
                }
            )
        ->singleton();

        $container->bind('_session_cache', function () {
            $driver = config('cache.session.driver');
                return new $driver(config('cache.session.path'));
        });
    }
}
