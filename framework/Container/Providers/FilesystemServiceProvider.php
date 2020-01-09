<?php

namespace Amber\Container\Providers;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Adapter\Local;
use Psr\Container\ContainerInterface;

class FilesystemServiceProvider extends ServiceProvider
{
    public function setUp(ContainerInterface $container): void
    {
        $container->register(FilesystemInterface::class, Filesystem::class)
            ->setArgument(
                '__construct',
                AdapterInterface::class,
                function () {
                    return new Local(config('filesystem.main.path'));
                }
            );
    }
}
