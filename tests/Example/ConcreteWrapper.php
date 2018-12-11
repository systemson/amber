<?php

namespace Tests\Example;

use Amber\Utils\Implementations\AbstractWrapper;

class ConcreteWrapper extends AbstractWrapper
{
    /**
     * To expose publicy a method, the method should be declared protected.
     *
     * @var array The method(s) that should be publicly exposed.
     */
    protected static $passthru = [
    	'getId',
    ];

    public static function setArguments(...$args): void
    {
    	//
    }

    public static function getArguments(): array
    {
    	return [];
    }
}