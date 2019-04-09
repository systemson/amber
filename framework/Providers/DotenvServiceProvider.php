<?php

namespace Amber\Framework\Providers;

use Dotenv\Dotenv;

class DotenvServiceProvider extends ServiceProvider
{
    public static function boot(): void
    {
        $dotenv = Dotenv::create(APP_DIR);
        $dotenv->load();
    }
}
