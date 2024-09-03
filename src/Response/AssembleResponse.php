<?php

namespace Sunhill\Framework\Response;

use Sunhill\Framework\Response\AbstractResponse;
use Sunhill\Framework\Plugins\Exceptions\FileNotFoundException;

abstract class AssembleResponse extends AbstractResponse
{

    protected $files = [];
    
    protected function prepareResponse()
    {    
        $result = '';
        sort($this->files);
        foreach ($this->files as $file) {
            $result .= file_get_contents($file);
        }
        return $result;
    }
 
    public function addFile(string $file)
    {
        if (!file_exists($file)) {
            throw new FileNotFoundException("The file '$file' was not found.");
        }
        $this->files[] = $file;
    }
    
    protected function doAddDir(string $dir)
    {
        $dir_obj = dir($dir);
        while (($entry = $dir_obj->read()) !== false) {
            if (($entry == '.') || ($entry == '..')) {
                continue;
            }
            if (is_dir($dir.'/'.$entry)) {
                $this->doAddDir($dir.'/'.$entry);
            } else {
                $this->files[] = $dir.'/'.$entry;
            }
        }
        $dir_obj->close();
    }
    
    public function addDir(string $dir)
    {
        if (!file_exists($dir)) {
            throw new FileNotFoundException("The dir '$dir' was not found.");
        }
        $this->doAddDir($dir);
    }
}