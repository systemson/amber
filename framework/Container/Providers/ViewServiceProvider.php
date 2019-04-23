<?php

namespace Amber\Framework\Container\Providers;

use Amber\Sketch\Sketch;
use Amber\Sketch\Template\Template;

class ViewServiceProvider extends ServiceProvider
{
    public function setUp(): void
    {
        $container = static::getContainer();

        $container->register(Sketch::class)
            ->afterConstruct('setViewsFolder', 'assets/views')
            ->afterConstruct('setCacheFolder', 'tmp/cache/views')
            ->afterConstruct(
                'setTemplate',
                function () use ($container) {
                    return $container->get(Template::class);
                }
            );
    }
}
