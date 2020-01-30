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

    protected function loadAppBinds(): void
    {
        foreach ((array) config('app.binds') as $service) {
            $this->bind($service);
        }
    }

    public function prepare(): void
    {
        /**
         * Load
         */
        $this->loadAppBinds();
        //$this->loadAppConfigs();
        $this->loadProviders();

        /**
         * Set up
         */
        $this->setUp();
        $this->setUpProviders();

        /**
         * Boot
         */
        $this->boot();
        $this->bootProviders();
    }

    /**
     * Set up the service providers.
     */
    private function setUp(): void
    {
        // Binds the container interface to itself.
        $this->register(ContainerInterface::class)
            ->setInstance($this)
        ;
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

    /**
     * Boots the application.
     */
    public function boot(): void
    {
        // Passes the container to the container aware class.
        ContainerAwareClass::setContainer($this);

        // Passes the container to the container facade.
        ContainerFacade::setContainer($this);
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
        $this->tearDown();
        $this->tearDownProviders();
        die();
    }

    /**
     * Tears down the application.
     */
    private function tearDown(): void
    {
    }

    /**
     * Tears down the service providers.
     */
    private function tearDownProviders(): void
    {
        array_map(
            function ($service) {
                $service->tearDown();
            },
            $this->providers
        );
    }
}
