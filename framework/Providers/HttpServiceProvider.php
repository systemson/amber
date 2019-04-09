<?php

namespace Amber\Framework\Providers;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\HttpFoundation\Session\Session;
use Amber\Framework\Auth\UserProvider;
use Amber\Framework\Auth\AuthClass;

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
        });

        $container->register(Session::class)
        ->singleton();

        dump($container->get(Session::class)->all());

        $container->locate(AuthClass::class)
        ->afterConstruct('setUser', function () use ($container) {
            $session = $container->get(Session::class);

            if ($session->has('_user')) {
                return $session->get('_user');
            } elseif ($session->has('_token')) {
                $userProvider = $container->get(UserProvider::class);
                $token = $session->get('_token');
                if (!is_null($token)) {
                    return $userProvider->getUserByToken($token);
                }
            }
        });
    }
}
