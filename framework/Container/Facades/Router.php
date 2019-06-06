<?php

namespace Amber\Framework\Container\Facades;

use Amber\Framework\Container\ContainerFacade;
use Amber\Framework\Http\Routing\Router as Accessor;

class Router extends ContainerFacade
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

    public static function boot(): void
    {
        $routes = self::getInstance();

        self::loadWebRoutes($routes);
        self::loadApiRoutes($routes);
    }

    public static function loadWebRoutes($routes)
    {
        $routes->group(function ($routes) {
            include CONFIG_DIR . '/routes.php';
        },
        [
            'middlewares' => [
                'Amber\Framework\Http\Server\Middleware\AuthMiddleware',
                'Amber\Framework\Http\Server\Middleware\CsfrMiddleware',
            ],
        ]);
    }

    public static function loadApiRoutes($routes)
    {
    }
}
