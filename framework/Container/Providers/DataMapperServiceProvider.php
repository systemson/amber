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

        $container->bind(Schema::class, function () {
            return Manager::schema();
        });

        $container->register(QueryBuilder::class)
            ->setArgument('db', function () {
                return getenv('DB_DRIVER', 'pgsql');
            })
        ;

        $container->register(Gemstone::class)
            ->afterConstruct('setMediators', [
            'pgsql' => SqlMediator::class,
            'array' => ArrayMediator::class,
            ])
        ;
    }

    public function setDown(): void
    {
    }
}
