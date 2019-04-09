<?php

namespace Amber\Framework\Providers;

use Amber\Framework\Container\StaticContainerAwareTrait;

class ServiceProvider
{
    use StaticContainerAwareTrait;

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
