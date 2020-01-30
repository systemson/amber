<?php

namespace Amber\Container\Providers;

use Symfony\Component\Routing\RequestContext;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Amber\Http\Routing\Context;
use Amber\Http\Routing\Matcher;
use Amber\Http\Message\Response;
use Amber\Http\Message\ResponseFactory;
use Amber\Http\Routing\Router;
use Amber\Http\Message\ServerRequest;
use Amber\Http\Message\Uri;
use Amber\Http\Security\Csrf;
use Amber\Http\Server\PipeHandler as RequestHandler;
use Amber\Http\Server\ResponseDispatcher;
use Amber\Http\Session\Session;
use App\Models\UserProvider;
use Amber\Auth\UserProviderContract;
use Amber\Auth\AuthClass;
use Sunrise\Stream\StreamFactory;
use Psr\Container\ContainerInterface;
use Amber\Container\Facades\Router as RouterFacade;

class HttpServiceProvider extends ServiceProvider
{
    public function boot(ContainerInterface $container): void
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

        $container->bind(ServerRequestInterface::class, ServerRequest::fromGlobals());

        $container->register(ResponseInterface::class, Response::class);
        $container->register(RequestHandlerInterface::class, RequestHandler::class)
            ->setArgument('__construct', 'middlewares', config('app.middlewares'))
        ;
        $container->register(UriInterface::class, Uri::class);
        $container->register(ResponseFactoryInterface::class, ResponseFactory::class);
        $container->bind(ResponseDispatcher::class);
        $container->bind(StreamFactoryInterface::class, StreamFactory::class);
        $container->bind(UserProviderContract::class, UserProvider::class);

        RouterFacade::boot();
    }
}
