<?php

return [

    // App binds
    'binds' => [
        Amber\Framework\Auth\AuthClass::class,
        Amber\Framework\Auth\UserProvider::class,
        Symfony\Component\Routing\Matcher\UrlMatcher::class,
        Symfony\Component\HttpFoundation\Response::class,
        Amber\Framework\Dispatch::class,
        Amber\Sketch\Template\Template::class,
        //Symfony\Component\Cache\Simple\FilesystemCache::class,
    ],

    'providers' => [
        Amber\Framework\Providers\DotenvServiceProvider::class,
        Amber\Framework\Providers\DebugServiceProvider::class,
        Amber\Framework\Providers\ModelServiceProvider::class,
        Amber\Framework\Providers\HttpServiceProvider::class,
        Amber\Framework\Providers\LoggerServiceProvider::class,
        Amber\Framework\Providers\FilesystemServiceProvider::class,
        Amber\Framework\Providers\ViewServiceProvider::class,
    ]
];
