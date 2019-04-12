<?php

define('CONFIG_DIR', __DIR__);
define('APP_DIR', CONFIG_DIR . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);
define('PUBLIC_DIR', APP_DIR . DIRECTORY_SEPARATOR . 'public');


if (!function_exists('config')) {
    function config(string $name)
    {
        $path = CONFIG_DIR . DIRECTORY_SEPARATOR . $name . '.php';

        if (file_exists($path)) {
            return (object) include $path;
        }
        return null;
    }
}

if (!function_exists('dd')) {
    function dd(... $value)
    {
        call_user_func_array('dump', $value);
        die();
    }
}
