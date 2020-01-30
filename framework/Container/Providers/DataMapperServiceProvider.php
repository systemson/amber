<?php

namespace Amber\Container\Providers;

use Illuminate\Database\Schema\Builder as Schema;
use Illuminate\Database\Capsule\Manager;
use Amber\Model\Gemstone;
use Amber\Model\Mediator\SqlMediator;
use Amber\Model\Mediator\ArrayMediator;
use Amber\Model\QueryBuilder\QueryBuilder;
use Aura\SqlQuery\QueryFactory;
use Aura\Sql\ExtendedPdo;
use Psr\Container\ContainerInterface;

class DataMapperServiceProvider extends ServiceProvider
{
    public function boot(ContainerInterface $container): void
    {
        $default = config('database.default');

        $container->register(QueryFactory::class)
            ->setArgument('__construct', 'db', $default)
        ;

        $container->bind(Schema::class, function () {
            return Manager::schema();
        });

        $container->register(QueryBuilder::class);

        $container->register(Gemstone::class)
            ->afterConstruct('setMediators', [
            'sql' => SqlMediator::class,
            'array' => ArrayMediator::class,
            ])->afterConstruct('addConnection', 'default', config("database.connections.{$default}"))
            ->afterConstruct('addConnections', config('database.connections'))
        ;
    }
}
