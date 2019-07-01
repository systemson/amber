<?php

namespace Amber\Dispatch;

use Amber\Container\Container;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Amber\Response;

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
        try {
            $matcher = $this->container->get(UrlMatcher::class);

            $default = (object) $matcher->matchRequest($this->getRequest());
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
        $callback = $this->container->getClosureFor($default->_controller, $default->_action);

        return $callback();
    }

    protected function handleClosure(Closure $callback)
    {
        return $callback();
    }
}
