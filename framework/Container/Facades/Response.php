<?php

namespace Amber\Framework\Container\Facades;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Amber\Framework\Container\ContainerFacade;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Psr\Http\Message\ResponseFactoryInterface;

class Response extends ContainerFacade
{
    /**
     * @var string The class accessor.
     */
    protected static $accessor = ResponseFactoryInterface::class;

    /**
     * @var mixed The instance of the accessor.
     */
    protected static $instance;

    /**
     * To expose publicy a method it should be declared protected.
     *
     * @var array The method(s) that should be publicly exposed.
     */
    protected static $passthru = [];
}
