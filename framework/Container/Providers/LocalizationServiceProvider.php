<?php

namespace Amber\Framework\Container\Providers;

use Illuminate\Database\Capsule\Manager as Eloquent;
use Amber\Framework\Helpers\Localization\Lang;
use Amber\Validator\Validator;

class LocalizationServiceProvider extends ServiceProvider
{
    public function setUp(): void
    {
        $this->getContainer()->register(Lang::class)
            ->setArguments([
                'folder' => ASSETS_FOLDER . 'lang',
                'default' => 'es',
                'fallback' => 'en',
            ])
        ;

        $lang = $this->getContainer()->get(Lang::class);

        // This validator must change to instance.
        Validator::setMessages([
            'email' => $lang->translate('validations.email'),
            'length' => $lang->translate('validations.length'),
        ]);
    }
}
