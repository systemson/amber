<?php

define('CONFIG_DIR', __DIR__);
define('APP_DIR', CONFIG_DIR . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);
define('PUBLIC_DIR', APP_DIR . 'public' . DIRECTORY_SEPARATOR);
define('TMP_DIR', APP_DIR . 'tmp' . DIRECTORY_SEPARATOR);


if (!function_exists('config')) {
    function config(string $slug, $default = null)
    {
        static $collection;

        if (is_null($collection)) {
            $collection = new Amber\Collection\MultilevelCollection();
        }

        $name = explode('.', $slug)[0];

        if ($collection->hasNot($name)) {
            $path = CONFIG_DIR . DIRECTORY_SEPARATOR . $name . '.php';

            if (file_exists($path)) {
                $configs = include $path;
            }

            $collection[$name] = $configs;
        }

        return $collection->get($slug) ?? $default;
    }
}

if (!function_exists('d')) {
    function d(... $value)
    {
        return call_user_func_array('dump', $value);
    }
}

if (!function_exists('dd')) {
    function dd(... $value)
    {
        call_user_func_array('dump', $value);
        die();
    }
}


if (!function_exists('carbon')) {
    function carbon()
    {
        return new Carbon\Factory([
            'locale' => config('app.date_locale', 'es_ES'),
            'timezone' => config('app.timezone', 'America/Caracas'),
        ]);
    }
}
