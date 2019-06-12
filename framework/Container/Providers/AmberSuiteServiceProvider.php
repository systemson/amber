<?php

namespace Amber\Framework\Container\Providers;

use Illuminate\Database\Capsule\Manager as Eloquent;
use Amber\Collection\Collection;

class AmberSuiteServiceProvider extends ServiceProvider
{
    public function setUp(): void
    {
        $container = static::getContainer();

        $container->bind(Collection::class);
    }

    public function setDown(): void
    {
        dd('lol');
    }
}
