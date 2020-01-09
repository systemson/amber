<?php

namespace Amber\Container\Providers;

use Illuminate\Database\Capsule\Manager as Eloquent;
use Amber\Helpers\Localization\Lang;
use Amber\Validator\Validator;
use Amber\Phraser\Phraser as Str;
use Amber\Container\Facades\Str as StrFacade;
use Symfony\Component\Inflector\Inflector;
use Psr\Container\ContainerInterface;

class LocalizationServiceProvider extends ServiceProvider
{
    public function setUp(ContainerInterface $container): void
    {
        $container->register(Lang::class)
            ->setArguments(
                '__construct',
                [
                    'folder' => path('assets', 'lang'),
                    'default' => config('app.locale', 'es'),
                    'fallback' => config('app.fallback_locale', 'en'),
                ]
            )
        ;

        $lang = $container->get(Lang::class);



        // This validator must change to instance.
        Validator::setMessages($lang->translate('validations.messages'));
        Validator::setAttributes($lang->translate('validations.attributes'));

        $container->register(Str::class)
            ->setArgument('__construct', 'string', '')
        ;

        StrFacade::setMacro('faker', function (string $locale = null) {
            if (is_null($locale)) {
                $locale = config('app.faker_locale');
            }

            return \Faker\Factory::create($locale);
        });

        StrFacade::setMacro('plural', function (string $singular) {
            $plural = Inflector::pluralize($singular);

            if (is_array($plural)) {
                return end($plural);
            }

            return $plural;
        });

        StrFacade::setMacro('singular', function (string $plural) {
            $singular = Inflector::singularize($plural);

            if (is_array($singular)) {
                return end($singular);
            }

            return $singular;
        });

        StrFacade::setMacro('alias', function (string $name, string $alias = null) {

            static $collection = [];

            if ($alias == null) {
                return $collection[$name] ?? $name;
            }

            $collection[$name] = $alias;
        });


        StrFacade::alias('alpha', 'alpha:áéíóúÁÉÍÓÚñÑ\'');
    }
}
