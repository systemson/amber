<?php

$routes->get('/api', function () {
	$version = Amber\Framework\Container\Facades\Amber::version();

	return Amber\Framework\Container\Facades\Response::json([
		'message' => $version,
	]);
});