<?php

namespace Amber\Container\Providers;

use Amber\Container\StaticContainerAwareTrait;
use Amber\Container\ContainerAwareClass;

class ServiceProvider extends ContainerAwareClass
{
    public static function boot(): void
    {
        //
    }

    public function setUp(): void
    {
        //
    }

    public function binds(): array
    {
        return [];
    }

    public function setDown(): void
    {
        //
    }
}
