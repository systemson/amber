<?php

return [

	// App binds
	'binds' => [
	    Amber\Framework\Dispatch::class,
	    Symfony\Component\Routing\Matcher\UrlMatcher::class,
	    Symfony\Component\HttpFoundation\Response::class,
	],

	// Singleton binds
    'singleton' => [
	    Symfony\Component\Routing\RouteCollection::class  => Amber\Framework\Route::getInstance(),
	    Symfony\Component\HttpFoundation\Request::class   => Symfony\Component\HttpFoundation\Request::createFromGlobals(),
    ],

    'providers' => [
    ]
];