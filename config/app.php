<?php

return [

    // App binds
    'binds' => [
        Amber\Framework\Dispatch::class,
        Amber\Sketch\Template\Template::class,
        Symfony\Component\Routing\Matcher\UrlMatcher::class,
        Symfony\Component\HttpFoundation\Response::class,
    ],

    'providers' => [
    	Amber\Framework\Providers\HttpServiceProvider::class,
    ]
];
