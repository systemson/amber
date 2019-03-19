<?php

namespace Amber\Framework;

use Amber\Framework\Application;
use Amber\Container\Injector as Container;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class Dispatch
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function context()
    {
        $context = new RequestContext();
        $context->fromRequest(Application::get(Request::class));

        return $context;
    }

    public function matcher()
    {
        return new UrlMatcher($this->container->get('_routes'), $this->context());
    }

    public function default()
    {
    	try {
        	return $this->matcher()->match($this->container->get(Request::class)->getRequestUri());
    	} catch (ResourceNotFoundException $e) {
    		
    	}
    }

    public function controller()
    {
        $class = $this->default()['_controller'];
        $this->container->bind($class);
        return $this->container->get($class);
    }

    public function action()
    {
        return $this->default()['_action'];
    }

    public function response()
    {
        $controller = $this->controller();
        
        $response = $controller->{$this->action()}();
        $response->prepare(Application::get(Request::class));

        return $response;
    }
}
