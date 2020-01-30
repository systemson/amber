<?php

namespace Amber\Container\Providers;

use Amber\Container\StaticContainerAwareTrait;
use Amber\Container\ContainerAwareClass;
use Psr\Container\ContainerInterface;

class ServiceProvider extends ContainerAwareClass
{
    public static function setUp(): void
    {
        //
    }

    public function binds(): array
    {
        return [];
    }

    public function boot(ContainerInterface $container): void
    {
        //
    }

    public function tearDown(): void
    {
        //
    }

    final public function register(ContainerInterface $container): self
    {
        $container->bindMultiple($this->binds());

        return $this;
    }
}
