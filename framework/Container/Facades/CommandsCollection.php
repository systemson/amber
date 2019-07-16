<?php

namespace Amber\Container\Facades;

use Amber\Container\ContainerFacade;
use Amber\Collection\Collection as Accessor;
use Amber\Utils\Traits\SingletonTrait;

class CommandsCollection extends ContainerFacade
{
    use SingletonTrait;

    /**
     * @var string The class accessor.
     */
    protected static $accessor = Accessor::class;

    /**
     * Runs after the class constructor.
     *
     * @return void
     */
    public static function afterConstruct(): void
    {
        static::setMultiple(static::commands());
    }

    public static function commands(): array
    {
        return [
            AppCacheCommand::class,
            MigrateDownCommand::class,
            MigrateRestartCommand::class,
            MigrateSeedsCommand::class,
            MigrateUpCommand::class,
            ServerCommand::class,
        ];
    }
}
