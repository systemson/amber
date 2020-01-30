<?php

namespace Amber\Container\Providers;

use Dotenv\Dotenv;

class DotenvServiceProvider extends ServiceProvider
{
    public static function setUp(): void
    {
        if (file_exists(path('.env'))) {
            $dotenv = Dotenv::create(path());
            $dotenv->load();
        }
    }
}
