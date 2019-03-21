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

    protected $request;

    public function __construct(Container $container, Request $request)
    {
        $this->container = $container;
        $this->request = $request;
    }

    public function getRequest()
    {
    	return $this->request;
    }

    public function response()
    {
    	$request = $this->getRequest();

        try {
        	$matcher = $this->container->get(UrlMatcher::class);
        	$uri = $request->getRequestUri();

            $default = (object) $matcher->match($uri);

        } catch (ResourceNotFoundException $e) {
            return Response::notFound($e->getMessage());
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
    	$controller = $default->_controller;

        $this->container->bind($controller);
        $controller = $this->container->get($controller);

        return $controller->{$default->_action}();
    }

    protected function handleClosure(Closure $callback)
    {
        return $callback();
    }
}
