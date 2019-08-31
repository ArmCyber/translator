<?php

namespace Zakhayko\Translator\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static getContent()
 * @method static translate(string $key, array $replace = [], $locale=null, $add_to_json=true)
 * @method static deleteTranslation(string $key)
 */

class Translator extends Facade {
    protected static function getFacadeAccessor()
    {
        return 'Zakhayko\Translator\TranslatorContainer';
    }
}
