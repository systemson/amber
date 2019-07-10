<?php

namespace Amber\Container\Providers;

use Illuminate\Database\Capsule\Manager as Eloquent;

class ModelServiceProvider extends ServiceProvider
{
    public static function boot(): void
    {
        $connection = config('database.default');

        $configs = config("database.connections.{$connection}");

        $eloquent = new Eloquent();

        $eloquent->addConnection($configs);
        $eloquent->setAsGlobal();
        $eloquent->bootEloquent();
    }
}
