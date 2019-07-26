<?php

namespace Zakhayko\Translator;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

class TranslatorContainer
{
    private $filename = false;
    private $content;
    private $enabled;
    private $sub_dirname;

    private function key($key){
        if (!$this->sub_dirname) $this->sub_dirname = config('translator.sub_dirname').'/';
        return $this->sub_dirname.$key;
    }

    private function pullContent(){
        $this->filename = config('translator.json_path');
        if (!file_exists($this->filename)) $this->content = [];
        else {
            $this->content = json_decode(file_get_contents($this->filename), true);
        }
    }

    public function getContent(){
        if (!$this->filename) $this->pullContent();
        return $this->content;
    }

    private function putContent(){
        if (!$this->filename) $this->pullContent();
        file_put_contents($this->filename, json_encode($this->content, JSON_UNESCAPED_UNICODE));
    }

    private function isEnabled(){
        if ($this->enabled===null) $this->enabled = config('translator.enabled');
        return $this->enabled;
    }
    
    private function makeReplacements($line, $replace) {
        if (count($replace)) {
            foreach ($replace as $key => $value) {
                $line = str_replace(
                    [':'.$key, ':'.Str::upper($key), ':'.Str::ucfirst($key)],
                    [$value, Str::upper($value), Str::ucfirst($value)],
                    $line
                );
            }
        }
        return $line;
    }

    /**
     * @param $key
     * @param array $replace
     * @param $locale
     * @param $add_to_json
     * @return array|string|null
     * @throws \Exception
     */
    public function translate(string $key, array $replace = [], $locale=null, $add_to_json=true){
        if (strpos($key, '.')===false) $key = 'app.'.$key;
        $realKey = $this->key($key);
        if (Lang::hasForLocale($realKey, $locale)) return Lang::get($realKey, $replace, $locale);
        if (strpos($key, '[')!==false || strpos($key, ']')!==false) throw new \Exception('Dont use these characters: "], [".');
        [$file, $key] = $explode = explode('.', $key, 4);
        $index = $explode[2]??null;
        if ($this->isEnabled() && $add_to_json) {
            if (!$this->filename) $this->pullContent();
            $fileContent = $this->content[$file]??[];
            if (array_key_exists($key, $fileContent)) {
                $thisKey = $fileContent[$key];
                if ($index) {
                    if (!is_array($thisKey)) throw new \Exception($key.'.'.$index.' is not array.');
                    if (!in_array($index, $thisKey)) {
                        $fileContent[$key][] = $index;
                        $this->content[$file] = $fileContent;
                        $this->putContent();
                    }
                }
                else if (is_array($thisKey)) return [];
            }
            else {
                if ($index) $fileContent[$key] = [$index];
                else $fileContent[$key] = true;
                $this->content[$file] = $fileContent;
                $this->putContent();
            }
        }
        return $this->makeReplacements($index??$key, $replace);
    }
}