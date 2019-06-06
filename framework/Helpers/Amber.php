<?php

namespace Amber\Framework\Helpers;

use Carbon\Carbon;
use Amber\Framework\Container\Application;
use Psr\Http\Message\ServerRequestInterface as Request;
use Amber\Framework\Http\Server\Middleware\CsfrMiddleware;

class Amber
{
    const VERSION = 'v-dev';
    const NAME = 'Amber Framework';

    public function version(): string
    {
        return self::VERSION;
    }

    public function name(): string
    {
        return self::NAME;
    }

    public function lap(): float
    {
        return number_format(microtime(true) - INIT_TIME, 6);
    }

    public function date(): Carbon
    {
        return Carbon::now();
    }

    public function csrf()
    {
        return Application::get(CsfrMiddleware::TOKEN_NAME);
    }
}
