<?php

namespace Amber\Container\Facades;

use Amber\Container\ContainerFacade;
use Amber\Collection\Collection as Accessor;

class CommandsCollection extends ContainerFacade
{
    /**
     * @var string The class accessor.
     */
    protected static $accessor = Accessor::class;

    /**
     * @var mixed The instance of the accessor.
     */
    protected static $instance;

    /**
     * To publicly expose a method it must be public or protected.
     *
     * @var array The method(s) that should be publicly exposed. An empty array means all.
     */
    protected static $passthru = [];

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
