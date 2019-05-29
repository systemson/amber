<?php

namespace Amber\Framework\Container\Facades;

use Symfony\Component\Routing\Route as SymfonyRoute;
use Amber\Framework\Http\Routing\Route as AmberRoute;
use Amber\Framework\Http\Routing\Router as AmberRouter;
use Amber\Framework\Container\ContainerFacade;
use Amber\Framework\Application;
use Symfony\Component\Routing\RouteCollection;
use Amber\Phraser\Phraser;
use Amber\Phraser\Str;

class Router extends ContainerFacade
{
    /**
     * @var string The class accessor.
     */
    protected static $accessor = AmberRouter::class;

    /**
     * @var mixed The instance of the accessor.
     */
    protected static $instance;

    /**
     * To expose publicy a method it should be declared public or protected.
     *
     * @var array The method(s) that should be publicly exposed.
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
                'Amber\Framework\Http\Server\Middleware\SessionMiddleware',
                'Amber\Framework\Http\Server\Middleware\CsfrMiddleware',
            ],
        ]);
    }

    public static function loadApiRoutes($routes)
    {
    }
}
