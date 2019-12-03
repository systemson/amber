<?php

namespace Amber\Helpers\Assets;

use Carbon\Carbon;
use Amber\Container\Application;
use Psr\Http\Message\ServerRequestInterface as Request;
use Amber\Http\Server\Middleware\CsfrMiddleware;

class Loader
{
    protected $packages = [];

    public function __construct($packages)
    {
        foreach ($packages as $vendor => $package) {
            $this->packages[$vendor] = $package;
        }
    }

    public function js($package)
    {
        $array = explode('\\', $package, 3);

        $class = $array[0] . '\\' . $array[1] . '\\' . 'Provider';

        $provider = new $class();
        return file_get_contents($provider->js()['jQuery']);
    }
}
