<?php

return [

	'default' => 'web',

	'routes' => [

		'web' => [
			'path' => CONFIG_DIR . '/routes/web.php',
			'middlewares' => [
				'Amber\Framework\Http\Server\Middleware\AuthMiddleware',
                'Amber\Framework\Http\Server\Middleware\CsfrMiddleware',
			],
		],

		'api' => [
			'path' => CONFIG_DIR . '/routes/api.php',
			'middlewares' => [
			],
		],

	],
];
