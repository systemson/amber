<?php

namespace Amber\Container\Providers;

use Illuminate\Database\Capsule\Manager as Eloquent;
use Amber\Helpers\Localization\Lang;
use Amber\Validator\Validator;
use Amber\Phraser\Phraser;
use Amber\Phraser\Str;
use Amber\Container\Facades\Str as FacadeStr;

class LocalizationServiceProvider extends ServiceProvider
{
    public function setUp(): void
    {
        $container = $this->getContainer();

        $container->register(Lang::class)
            ->setArguments([
                'folder' => ASSETS_FOLDER . 'lang',
                'default' => config('app.locale', 'es'),
                'fallback' => config('app.fallback_locale', 'en'),
            ])
        ;

        $lang = $container->get(Lang::class);



        // This validator must change to instance.
        Validator::setMessages($lang->translate('validations.messages'));
        Validator::setAttributes($lang->translate('validations.attributes'));

        $container->register(Str::class)
            ->setArgument('string', '')
        ;

        FacadeStr::setMacro('faker', function (string $locale = null) {
            if (is_null($locale)) {
                $locale = config('app.faker_locale');
            }

            return \Faker\Factory::create($locale);
        });
    }
}
