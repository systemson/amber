<?php

namespace Amber\Container;

use Amber\Container\Container;
use Psr\Container\ContainerInterface;
use Amber\Http\Server\ResponseDispatcher;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\SimpleCache\CacheInterface;

class Bootstrap extends Container
{
    protected $providers = [];

    protected function loadProviders(): void
    {
        $this->providers = (array) config('app.providers');
    }

    protected function loadBinds(): void
    {
        foreach ((array) config('app.binds') as $service) {
            $this->bind($service);
        }
    }

    public function prepare(): void
    {
        if (false) {
           // $this->pickUp();
        }

        // Binds the container interface to itself.
        $this->register(ContainerInterface::class)
            ->setInstance($this)
        ;

        // Loads the default binds.
        $this->loadBinds();

        // Loads the app providers.
        $this->loadProviders();

        // Warm up the providers.
        $this->setUpProviders();

        // Passes the container to the container aware class.
        ContainerAwareClass::setContainer($this);

        // Passes the container to the container facade.
        ContainerFacade::setContainer($this);

        $this->bootProviders();

        //$this->drop();
    }

    /**
     * Boots the service providers.
     */
    private function bootProviders(): void
    {
        array_map(
            function ($service) {
                $service
                    ->register($this)
                    ->boot($this)
                ;
            },
            $this->providers
        );
    }

    /**
     * Set up the service providers.
     */
    private function setUpProviders(): void
    {
        foreach ($this->providers as $index => $class) {
            $class::setUp();
            $this->providers[$index] = $this->make($class);
        }
    }

    public function run()
    {
        $this->get(ResponseDispatcher::class)->send(
            $this->get(RequestHandlerInterface::class)->handle(
                $this->get(ServerRequestInterface::class)
            )
        );
    }

    /**
     * Shuts downs the application after the response is dispatched.
     */
    public function shutDown(): void
    {
        $this->shutDownProviders();
    }

    /**
     * Shuts down the service providers.
     */
    private function shutDownProviders(): void
    {
        array_map(
            function ($service) {
                $service->tearDown();
            },
            $this->providers
        );
    }

    protected function drop(): void
    {
        if (false) {
            return;
        }
        $cache = $this->get(CacheInterface::class);
        $cache->set('__container_cache', $this->getCollection()->toArray());
    }
}
