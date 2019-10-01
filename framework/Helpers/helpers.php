<?php

if (!function_exists('path')) {
    function path(...$paths)
    {
        return realpath(
            BASE_DIR . DIRECTORY_SEPARATOR .
            implode(
                DIRECTORY_SEPARATOR,
                str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $paths)
            )
        );
    }
}

if (!function_exists('config')) {
    function config(string $slug, $default = null)
    {
        static $collection;

        if (is_null($collection)) {
            $collection = new Amber\Collection\MultilevelCollection();
        }

        $name = explode('.', $slug)[0];
        if ($collection->hasNot($name)) {
            $path = path('config', $name . '.php');

            if (file_exists($path)) {
                $configs = include $path;
                $collection[$name] = $configs;
            }
        }

        return $collection->get($slug) ?? $default;
    }
}

if (!function_exists('env')) {
    function env(string $var, $default = null)
    {
        static $collection;

        if ($value = getenv($var)) {
            return $value;
        }

        return $default;
    }
}

if (!function_exists('lang')) {
    function lang(string $slug)
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

        return $collection->get($slug) ?? $slug;
    }
}

if (!function_exists('d')) {
    function d(...$value)
    {
        return call_user_func_array('dump', $value);
    }
}

if (!function_exists('dd')) {
    function dd(...$value)
    {
        call_user_func_array('dump', $value);
        die();
    }
}


if (!function_exists('carbon')) {
    function carbon(string $tz = null)
    {
        return new Carbon\Factory([
            'timezone' => $tz ?? config('app.timezone', 'America/Caracas'),
            'locale' => config('app.date_locale', 'es_ES'),
        ]);
    }
}
