<?php

namespace Amber\Framework\Container\Providers;

use Symfony\Component\Routing\RequestContext;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Amber\Framework\Http\Routing\Context;
use Amber\Framework\Http\Routing\Matcher;
use Amber\Framework\Http\Message\Response;
use Amber\Framework\Http\Message\ResponseFactory;
use Amber\Framework\Http\Routing\Router;
use Amber\Framework\Http\Message\ServerRequest;
use Amber\Framework\Http\Message\Uri;
use Amber\Framework\Http\Security\Csrf;
use Amber\Framework\Http\Server\RequestHandler;
use Amber\Framework\Http\Server\ResponseDispatcher;
use Amber\Framework\Http\Session\Session;
use Amber\Framework\Auth\UserProvider;
use Amber\Framework\Auth\AuthClass;
use Sunrise\Stream\StreamFactory;

class HttpServiceProvider extends ServiceProvider
{
    public function setUp(): void
    {
        $container = static::getContainer();


        $container->singleton(Router::class);


        $container->bind(Matcher::class);

        $container
            ->singleton(RequestContext::class, Context::class)
            ->afterConstruct(
                'fromPrsRequest',
                function () use ($container) {
                    return $container->get(ServerRequestInterface::class);
                }
            )
        ;

        $container->singleton(Session::class);

        $container->register(ServerRequestInterface::class, ServerRequest::class);
        $container->register(ResponseInterface::class, Response::class);
        $container->register(RequestHandlerInterface::class, RequestHandler::class)
            ->setArgument('middlewares', config('app.middlewares'))
        ;
        $container->register(UriInterface::class, Uri::class);
        $container->register(ResponseFactoryInterface::class, ResponseFactory::class);
        $container->bind(ResponseDispatcher::class);
        $container->bind(StreamFactoryInterface::class, StreamFactory::class);
    }
}
