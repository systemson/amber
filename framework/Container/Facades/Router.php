<?php

namespace Amber\Container\Facades;

use Amber\Container\ContainerFacade;
use Amber\Http\Routing\Router as Accessor;
use Amber\Utils\Traits\SingletonTrait;

class Router extends ContainerFacade
{
    use SingletonTrait;

    /**
     * @var string The class accessor.
     */
    protected static $accessor = Accessor::class;

    public static function boot(): void
    {
        $routes = self::getInstance();

        self::loadWebRoutes($routes);
        self::loadApiRoutes($routes);
    }

    public static function loadWebRoutes($routes)
    {
        $routes->group(function ($routes) {
            include config('routes.routes.web.path');
        },
        [
            'middlewares' => config('routes.routes.web.middlewares'),
        ]);
    }

    public static function loadApiRoutes($routes)
    {
        $routes->group(function ($routes) {
            include config('routes.routes.api.path');
        },
        [
            'middlewares' => config('routes.routes.api.middlewares'),
            'prefix' => '/api',
            'namespace' => 'App\Controllers\Api\\',
        ]);
    }
}
