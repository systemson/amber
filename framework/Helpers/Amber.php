<?php

namespace Amber\Helpers;

use Carbon\Carbon;
use Amber\Container\Application;
use Psr\Http\Message\ServerRequestInterface as Request;
use Amber\Http\Server\Middleware\CsfrMiddleware;

class Amber
{
    const VERSION = 'v0.1.x-dev';
    const NAME = 'Amber Framework';

    public function version(): string
    {
        return self::VERSION;
    }

    public function name(): string
    {
        return self::NAME;
    }

    public function fullname()
    {
        return $this->name() . ' ' . $this->version();
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
        $token = Application::get(CsfrMiddleware::TOKEN_NAME);
        $name = CsfrMiddleware::TOKEN_NAME;

        return "<input type=\"hidden\" name=\"{$name}\" value=\"{$token}\">";
    }
}
