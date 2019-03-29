<?php


define('PUBLIC_DIR',         getcwd());
define('APP_DIR', PUBLIC_DIR . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);
define('CONFIG_DIR', APP_DIR . 'config');

if (!function_exists('config')) {
    function config(string $name)
    {
    	$path = CONFIG_DIR . DIRECTORY_SEPARATOR . $name . '.php';

    	if (file_exists($path)) {
    		return (object) require $path;
    	}
    	return null;
    }
}