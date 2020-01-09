<?php

namespace Amber\Container\Providers;

use Illuminate\Database\Capsule\Manager as Eloquent;
use Amber\Collection\Collection;
use Psr\Container\ContainerInterface;

class AmberSuiteServiceProvider extends ServiceProvider
{
    public function setUp(ContainerInterface $container): void
    {
        //
    }

    public function setDown(): void
    {
        //
    }
}
