<?php

namespace Amber\Container\Providers;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;
use Psr\SimpleCache\CacheInterface;
use Psr\Container\ContainerInterface;

class CacheServiceProvider extends ServiceProvider
{
    public function setUp(ContainerInterface $container): void
    {
        $container->bind(CacheInterface::class, function () {
            $adapter = new FilesystemAdapter('', 0, config('cache.default.path'));
            return new Psr16Cache($adapter);
        });

        $container->bind('_session_cache', function () {
            $adapter = new FilesystemAdapter('', 0, config('cache.session.path'));
            return new Psr16Cache($adapter);
        });
    }
}
