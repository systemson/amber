<?php

$routes->get('/api', function () {
    $version = Amber\Container\Facades\Amber::version();

    return Amber\Container\Facades\Response::json([
        'message' => $version,
    ]);
});

$routes->get('/api/users' , function () {

	$provider = new App\Models\UserProvider();

	 return Amber\Container\Facades\Response::json([
        $provider->all()
    ]);
});
