<?php

return [

    Symfony\Component\HttpFoundation\Request::class => Symfony\Component\HttpFoundation\Request::createFromGlobals(),
    '_routes' => Amber\Route\Route::getInstance(),
    '_dispatch' => Amber\Framework\Dispatch::class,

];