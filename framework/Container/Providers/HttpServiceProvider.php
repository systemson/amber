<?php

namespace Amber\Framework\Container\Providers;

use Symfony\Component\Routing\RouteCollection;
use Amber\Framework\Http\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\HttpFoundation\Session\Session;
use Amber\Framework\Auth\UserProvider;
use Amber\Framework\Auth\AuthClass;
use Amber\Framework\Container\Facades\Cache;
use Amber\Framework\Container\Facades\Session as SessionFacade;
use Psr\Http\Message\RequestHandlerInterface;
use Amber\Framework\Http\Server\RequestHandler;
use Psr\Http\Message\ServerRequestInterface;
use Amber\Framework\Http\Message\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Amber\Framework\Http\Message\Response;
use Psr\Http\Message\UriInterface;
use Amber\Framework\Http\Message\Uri;
use Psr\Http\Message\ResponseFactoryInterface;
use Amber\Framework\Http\Message\ResponseFactory;
use Amber\Framework\Http\Server\ResponseDispatcher;
use Amber\Framework\Helpers\Hash;
use Carbon\Carbon;
use Amber\Framework\Http\Security\Csrf;
use Sunrise\Stream\StreamFactory;
use Psr\Http\Message\StreamFactoryInterface;

class HttpServiceProvider extends ServiceProvider
{
    public function setUp(): void
    {
        $container = static::getContainer();


        $container->singleton(Router::class);

        $container
            ->register(RouteCollection::class)
            ->setInstance($container->get(Router::class)->all())
        ;

        $container
            ->register(Request::class)
            /*->setInstance(function () {
                Request::createFromGlobals();
            })*/
            ->setInstance(Request::createFromGlobals())
        ;

        $container
            ->singleton(RequestContext::class)
            ->afterConstruct(
                'fromRequest',
                function () use ($container) {
                    return $container->get(Request::class);
                }
            );

        $container->singleton(Session::class);

        $container->register(ServerRequest::class, ServerRequestInterface::class);
        $container->register(Response::class, ResponseInterface::class);
        $container->register(RequestHandler::class, RequestHandlerInterface::class)
            ->setArgument('middlewares', config('app')->middlewares)
        ;
        $container->register(Uri::class, UriInterface::class);
        $container->register(ResponseFactory::class, ResponseFactoryInterface::class);
        $container->bind(ResponseDispatcher::class);
        $container->bind(StreamFactoryInterface::class, StreamFactory::class);
        $container->singleton(Csrf::class);
    }
}
