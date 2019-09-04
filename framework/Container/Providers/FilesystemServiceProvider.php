<?php

namespace Amber\Container\Providers;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Adapter\Local;

class FilesystemServiceProvider extends ServiceProvider
{
    public function setUp(): void
    {
        $container = static::getContainer();

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
