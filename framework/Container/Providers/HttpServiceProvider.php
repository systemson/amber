<?php

namespace Amber\Framework\Container\Providers;

use Symfony\Component\{
    Routing\RequestContext,
    HttpFoundation\Session\Session
};
use Psr\Http\{
    Server\RequestHandlerInterface,
    Message\ServerRequestInterface,
    Message\ResponseInterface,
    Message\UriInterface,
    Message\ResponseFactoryInterface,
    Message\StreamFactoryInterface
};
use Amber\Framework\Http\{
    Message\ServerRequest,
    Message\Response,
    Message\Uri,
    Message\ResponseFactory,
    Routing\Matcher,
    Routing\Router,
    Security\Csrf,
    Server\RequestHandler,
    Server\ResponseDispatcher,
    Routing\Context
};
use Amber\Framework\Auth\UserProvider;
use Amber\Framework\Auth\AuthClass;
use Amber\Framework\Container\Facades\Cache;
use Amber\Framework\Container\Facades\Session as SessionFacade;
use Amber\Framework\Helpers\Hash;
use Carbon\Carbon;
use Sunrise\Stream\StreamFactory;

class HttpServiceProvider extends ServiceProvider
{
    public function setUp(): void
    {
        $container = static::getContainer();


        $container->singleton(Router::class);


        $container->bind(Matcher::class);

        $container
            ->singleton(Context::class, RequestContext::class)
            ->afterConstruct(
                'fromPrsRequest',
                function () use ($container) {
                    return $container->get(ServerRequestInterface::class);
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
