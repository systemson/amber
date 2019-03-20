<?php

namespace Amber\Framework;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Amber\Utils\Implementations\AbstractWrapper;
use Amber\Framework\Application;
use Symfony\Component\HttpFoundation\JsonResponse;

class Response extends AbstractWrapper
{
    /**
     * @var The class accessor.
     */
    protected static $accessor = SymfonyResponse::class;

    /**
     * @var object The instance of the accessor.
     */
    protected static $instance;

    /**
     * @todo MUST be moved to a ContainerAwareTrait
     *
     * @var The DI container.
     */
    protected static $container;

    /**
     * To expose publicy a method it should be declared protected.
     *
     * @var array The method(s) that should be publicly exposed.
     */
    protected static $passthru = [];

    public static function notFound()
    {
        $response = clone static::getInstance();

        $response->setStatusCode(200);
        $response->setContent('Not found!');

        return $response;
    }

    public static function json($data = null)
    {
        return new JsonResponse($data);
    }
}
