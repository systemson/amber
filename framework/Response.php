<?php

namespace Amber\Framework;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Amber\Utils\Implementations\AbstractWrapper;
use Amber\Framework\Application;
use Symfony\Component\HttpFoundation\JsonResponse;

class Response extends AbstractWrapper
{
    /**
     * @var string The class accessor.
     */
    protected static $accessor = SymfonyResponse::class;

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

    public static function notFound(string $message = 'Not found!')
    {
        $response = clone static::getInstance();

        $response->setStatusCode(404);
        $response->setContent($message);

        return $response;
    }

    public static function json($data = null)
    {
        return new JsonResponse($data);
    }
}
