<?php

namespace Amber\Framework\Container\Providers;

use Amber\Sketch\Sketch;
use Amber\Sketch\Template\Template;

class ViewServiceProvider extends ServiceProvider
{
    public function setUp(): void
    {
        $container = static::getContainer();

        $container->bind('Amber\Framework\Helpers\Amber');

        $container->singleton(Sketch::class)
            ->afterConstruct('setViewsFolder', 'assets/views')
            ->afterConstruct('setCacheFolder', 'tmp/framework/views')
            ->afterConstruct(
                'setTemplate',
                function () use ($container) {
                    return $container->get(Template::class);
                }
            )
            ->afterConstruct(
                'setTag',
                'authname',
                '<?= Amber\Framework\Container\Facades\Auth::name(); ?>'
            )
            ->afterConstruct(
                'setTag',
                'appname',
                '<?= getenv(\'APP_NAME\'); ?>'
            )
            ->afterConstruct(
                'setTag',
                'lap',
                '<?= number_format(microtime(true) - INIT_TIME, 6); ?>'
            )
            ->afterConstruct(
                'setTag',
                'appversion',
                '<?= Amber\Framework\Container\Facades\Amber::version(); ?>'
            )
            ->afterConstruct(
                'setTag',
                'csrf',
                '<?= Amber\Framework\Container\Facades\Amber::csrf(); ?>'
            )
            ->afterConstruct(
                'setTag',
                'auth_check',
                '<?php if (Amber\Framework\Container\Facades\Auth::check()) : ?>',
                '<?php endif; ?>'
            )
            ->afterConstruct(
                'setTag',
                'has_errors',
                '<?php if (Amber\Framework\Container\Facades\Session::flash()->has(\'errors\')) : ?>',
                '<?php endif; ?>'
            )
            ->afterConstruct('dev')
        ;
    }
}
