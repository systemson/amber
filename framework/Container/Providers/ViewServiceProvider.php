<?php

namespace Amber\Container\Providers;

use Amber\Sketch\Sketch;
use Amber\Sketch\Template\Template;
use Amber\Container\Facades\Session;

class ViewServiceProvider extends ServiceProvider
{
    public function setUp(): void
    {
        $container = static::getContainer();

        $container->bind(\Amber\Helpers\Amber::class);

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
                '<?= Amber\Container\Facades\Auth::name(); ?>'
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
                '<?= Amber\Container\Facades\Amber::version(); ?>'
            )
            ->afterConstruct(
                'setTag',
                'csrf',
                '<?= Amber\Container\Facades\Amber::csrf(); ?>'
            )
            ->afterConstruct(
                'setTag',
                'auth_check',
                '<?php if (Amber\Container\Facades\Auth::check()) : ?>',
                '<?php endif; ?>'
            )
            ->afterConstruct(
                'setTag',
                'has_errors',
                '<?php if (Amber\Container\Facades\Session::flash()->has(\'errors\')) : ?>',
                '<?php endif; ?>'
            )
            ->afterConstruct(
                'setTag',
                'show_errors',
                '<?php foreach($errors as $input => $error): ?>',
                '<?php endforeach; ?>'
            )
            ->afterConstruct(
                'setTag',
                'translate',
                '<?php echo Amber\Container\Facades\Lang::translate("',
                '"); ?>'
            )
            ->afterConstruct(
                'setGlobals',
                [
                    'errors' => Session::flash()->get('errors'),
                ]
            )
            ->afterConstruct('dev')
        ;
    }
}
