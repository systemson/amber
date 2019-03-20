<?php

return [

	// App binds
    Amber\Framework\Dispatch::class                     => Amber\Framework\Dispatch::class,
    Symfony\Component\Routing\Matcher\UrlMatcher::class => Symfony\Component\Routing\Matcher\UrlMatcher::class,
    Symfony\Component\HttpFoundation\Response::class    => Symfony\Component\HttpFoundation\Response::class,

    // Singleton binds
    Symfony\Component\HttpFoundation\Request::class  => Symfony\Component\HttpFoundation\Request::createFromGlobals(),
    Symfony\Component\Routing\RouteCollection::class => Amber\Route\Route::getInstance(),
];