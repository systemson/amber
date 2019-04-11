<?php

namespace Amber\Framework\Providers;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\HttpFoundation\Session\Session;
use Amber\Framework\Auth\UserProvider;
use Amber\Framework\Auth\AuthClass;
use Amber\Framework\Container\Facades\Cache;
use Amber\Framework\Container\Facades\Session as SessionFacade;
use Amber\Framework\Http\Server\Message\PsrSymfonyBridge\ServerRequest;
use Psr\Http\Message\ServerRequestInterface;
use Amber\Framework\Helpers\Hash;
use Carbon\Carbon;

class HttpServiceProvider extends ServiceProvider
{
    public function setUp(): void
    {
        $container = static::getContainer();

        $container->register(RouteCollection::class)
        ->singleton();

        $container->register(Request::class)
        ->setInstance(Request::createFromGlobals());

        $container->register(RequestContext::class)
        ->afterConstruct('fromRequest', function () use ($container) {
            return $container->get(Request::class);
        })
        ->singleton();

        $container->register(Session::class)
        ->singleton();


        // This should be moved to SessionMiddleware
        $container->locate(AuthClass::class)
        ->afterConstruct('setUser', function () use ($container) {

            if (SessionFacade::has('_token')) {
                $token = SessionFacade::get('_token');

                if (Cache::has($token)) {
                    return Cache::get($token);
                } else {
                    $userProvider = $container->get(UserProvider::class);
                    if (!is_null($token)) {
                        return $userProvider->getUserByToken($token);
                    }
                }
            }
        });

        dump(SessionFacade::all());
    }
}
