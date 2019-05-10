<?php

return [

    // App binds
    'binds' => [
        Amber\Framework\Auth\AuthClass::class,
        Amber\Framework\Auth\UserProvider::class,
        Symfony\Component\HttpFoundation\Response::class,
        Amber\Framework\Dispatch\Dispatch::class,
        Amber\Sketch\Template\Template::class,
        //Symfony\Component\Cache\Simple\FilesystemCache::class,
    ],

    'providers' => [
        Amber\Framework\Container\Providers\DotenvServiceProvider::class,
        Amber\Framework\Container\Providers\DebugServiceProvider::class,
        Amber\Framework\Container\Providers\ModelServiceProvider::class,
        Amber\Framework\Container\Providers\CacheServiceProvider::class,
        Amber\Framework\Container\Providers\HttpServiceProvider::class,
        Amber\Framework\Container\Providers\LoggerServiceProvider::class,
        Amber\Framework\Container\Providers\FilesystemServiceProvider::class,
        Amber\Framework\Container\Providers\ViewServiceProvider::class,
        Amber\Framework\Container\Providers\DataMapperServiceProvider::class,
        Amber\Framework\Container\Providers\AmberSuiteServiceProvider::class,
    ]
];
