<?php
if (!function_exists('t')){
    function t($key, array $replace = [], $locale=null, $add_to_json=true) {
        return Zakhayko\Translator\Facades\Translator::translate($key, $replace, $locale, $add_to_json);
    }
}