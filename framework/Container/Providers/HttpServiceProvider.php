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
use Amber\Framework\Http\Server\RouteHandler;
use Amber\Framework\Http\Server\AbstractRequestHandler;
use Psr\Http\Message\ServerRequestInterface;
use Amber\Framework\Http\Message\PsrSymfonyBridge\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Amber\Framework\Http\Message\Response;
use Psr\Http\Message\UriInterface;
use Amber\Framework\Http\Message\PsrSymfonyBridge\Uri;
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
        $container->register(AbstractRequestHandler::class, RequestHandlerInterface::class)
            ->setArgument('middlewares', config('app')->middlewares)
        ;
        $container->register(Uri::class, UriInterface::class);
        $container->register(ResponseFactory::class, ResponseFactoryInterface::class);
        $container->bind(ResponseDispatcher::class);
        $container->bind(RouteHandler::class);
        $container->bind(StreamFactoryInterface::class, StreamFactory::class);
        $container->singleton(Csrf::class);
    }
}
