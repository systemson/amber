<?php

namespace Amber\Container\Providers;

use Illuminate\Database\Schema\Builder as Schema;
use Illuminate\Database\Capsule\Manager;
use Amber\Model\Gemstone;
use Amber\Model\Mediator\SqlMediator;
use Amber\Model\Mediator\ArrayMediator;
use Amber\Model\QueryBuilder\QueryBuilder;

class DataMapperServiceProvider extends ServiceProvider
{
    public function setUp(): void
    {
        $container = static::getContainer();

        $default = config('database.default');

        $container->bind(Schema::class, function () {
            return Manager::schema();
        });

        $container->register(QueryBuilder::class)
            ->setArgument('db', function () use ($default) {
                return $default;
            })
            ->setArgument('common', QueryBuilder::COMMON)
        ;

        $container->register(Gemstone::class)
            ->afterConstruct('setMediators', [
            'pgsql' => SqlMediator::class,
            'array' => ArrayMediator::class,
            ])->afterConstruct('addConnection', 'default', config("database.connections.{$default}"))
            ->afterConstruct('addConnections', config('database.connections'))
        ;
    }

    public function setDown(): void
    {
    }
}
