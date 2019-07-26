<?php 
namespace Zakhayko\Translator;

use Zakhayko\Translator\Facades\Translator;

class ModelContainer {

    private $json;
    private $files;

    public function __construct()
    {
        $this->json = Translator::getContent();
    }

    private function export($var){
        if (is_array($var)) {
            $toImplode = [];
            foreach ($var as $key => $value) {
                $toImplode[] = var_export($key, true).'=>'.$this->export($value);
            }
            $code = '['.implode(',', $toImplode).']';
            return $code;
        } else return var_export($var, true);
    }

    /**
     * @param $dir
     * @param $filename
     * @param $result
     * @throws \Exception
     */
    private function write($dir, $filename, $result){
        $content = $this->export($result);
        if (!file_exists($dir)) mkdir($dir, 0775, true);
        $saved = file_put_contents($dir.$filename.'.php', '<?php return '.$content.';');
        if ($saved === false) throw new \Exception('Cant write data to file.');
    }

    public function getFiles(){
        if ($this->files === null) {
            $files = [];
            foreach($this->json as $filename=>$content) {
                $count = 0;
                foreach($content as $key) {
                    if (is_array($key)) $count+=count($key);
                    else $count+=1;
                }
                $files[] = ['filename'=>$filename, 'words_count'=>$count];
            }
            $this->files = $files;
        }
        return $this->files;
    }

    public function hasFile($filename) {
        return array_key_exists($filename, $this->json);
    }
    
    public function getFile($filename) {
        return $this->json[$filename]??null;
    }

    /**
     * @param $locale
     * @param $filename
     * @param $contents
     * @return bool
     * @throws \Exception
     */
    public function putContents($locale, $filename, $contents) {
        if (!$this->hasFile($filename)) throw new \Exception('File "'.$filename.'" does not exists.');
        $result = [];
        foreach($this->json[$filename] as $key => $sub) {
            if (!is_array($sub)) {
                $value = $contents[$key]??'';
                if (is_string($value)) $result[$key] = $value;
            }
            else {
                $content = [];
                foreach($sub as $sub_key) {
                    $value = $contents[$key][$sub_key]??'';
                    if (is_string($value)) $content[$sub_key] = $value;
                }
                $result[$key] = $content;
            }
        }
        $dir = resource_path('lang/').$locale.'/'.config('translator.sub_dirname').'/';
        $this->write($dir, $filename, $result);
        return true;
    }
}