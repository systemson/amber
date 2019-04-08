<?php

namespace Amber\Framework\Providers;


class HttpServiceProvider extends ServiceProvider
{
	public function setUp(): void
	{
		$container = static::getContainer();

		$container->bind(
        	\Symfony\Component\Routing\RouteCollection::class,
        	new \Symfony\Component\Routing\RouteCollection()
        );

        $container->bind(\Symfony\Component\HttpFoundation\Request::class, function () {
            return \Symfony\Component\HttpFoundation\Request::createFromGlobals();
        });

        $container->register(\Symfony\Component\Routing\RequestContext::class)
        ->afterConstruct('fromRequest', function () use ($container) {
            return $container->get(Request::class);
        });
	}
}