<?php

namespace Amber\Framework\Container\Providers;

use Illuminate\Database\Schema\Builder as Schema;
use Illuminate\Database\Capsule\Manager;

class DataMapperServiceProvider extends ServiceProvider
{
    public function setUp(): void
    {
        $container = static::getContainer();

        $container->bind(Schema::class, function () {
            return Manager::schema();
        });
    }
}
