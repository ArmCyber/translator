<?php

namespace Zakhayko\Translator\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static getFiles()
 * @method static hasFile($filename)
 * @method static getFile($filename)
 * @method static putContents($locale, $filename, $contents)
 */

class TranslatorModel extends Facade {
    protected static function getFacadeAccessor()
    {
        return 'Zakhayko\Translator\ModelContainer';
    }
}