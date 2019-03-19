<?php

namespace Amber\Framework;

use Amber\Container\Injector as Container;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Amber\Framework\Response;

class Dispatch
{
    /**
     * @var The DI container.
     */
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function response()
    {
        try {
        	$matcher = $this->container->get(UrlMatcher::class);
        	$request = $this->container->get(Request::class);
        	$uri = $request->getRequestUri();

            $default = (object) $matcher->match($uri);

        } catch (ResourceNotFoundException $e) {
            return Response::notFound();
        }


        if (isset($default->_controller)) {
        	return $this->handleClass($default);
        } elseif (isset($default->_callback)) {
        	return $this->handleClosure($default);
        }

        //return new Response(parse_str($default));
    }

    protected function handleClass($default)
    {
        $this->container->bind($default->_controller);
        $controller = $this->container->get($default->_controller);

        return $controller->{$default->_action}();
    }

    protected function handleClosure($callback)
    {
        return $callback();
    }
}
