<?php
/**
 * @file FileTracerBackend.php
 * Provides a tracer backend that stores the traces in a file system
 *
 * @author Klaus Dimde
 * ----------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2024-03-24
 * Localization: unknown
 * Documentation: all public
 * Tests:
 * Coverage: unknown
 * PSR-State: complete
 */

namespace Sunhill\Properties\Tracer\Backends;

use Sunhill\Properties\Tracer\AbstractTracerBackend;

class FileTracerBackend extends AbstractTracerBackend
{
    
    protected $tracer_dir;
    
    public function setTracerDir(string $dir)
    {
        $this->tracer_dir = $dir;
    }
    
    public function getTracerDir()
    {
        if (empty($this->tracer_dir)) {
           return env('TRACER_DIR'); 
        }
        return $this->tracer_dir;
    }
    
    private function writeEntry($file, $stamp, $value)
    {
        fputs($file, $stamp." ".$value."\n");
    }
    
    protected function doTrace(string $path, \StdClass $data, int $first_stamp)
    {
       if (($file = fopen($this->getTracerDir().'/'.$path,'x'))) {
           $this->writeEntry($file, $first_stamp, $data->value);
           fclose($file);
       }
    }
    
    protected function doUntrace(string $path)
    {
        unlink($this->getTracerDir().'/'.$path);
    }
    
    protected function getIsTraced(string $path): bool
    {
        return file_exists($this->getTracerDir().'/'.$path);
    }
    
    protected function doGetTracedElements(): array
    {
        $result = [];
        
        $dir = dir($this->getTracerDir());
        
        while (false !== ($entry = $dir->read())) {
            
            if  ($entry[0] != '.') {
                $result[] = $entry;
            }
        }
        
        $dir->close();
        
        sort($result);
        return $result;
    }
    
    protected function readAllValues(string $path): array
    {
        return array_map(function($element) {
            $result = new \StdClass();
            list($result->stamp,$result->value) = explode(' ',trim($element));
            return $result;
        }, file($this->getTracerDir().'/'.$path));
    }
    
    protected function getFirstPair(string $path): \StdClass
    {
        $all = $this->readAllValues($path);
        return $all[0];
    }
    
    protected function getLastPair(string $path): \StdClass
    {
        $all = $this->readAllValues($path);
        return $all[count($all)-1];
    }
    
    private function putValue(string $tracee, int $stamp, $value)
    {
        if (($file = fopen($this->getTracerDir().'/'.$tracee,'a+'))) {
            $this->writeEntry($file, $stamp, $value);
            fclose($file);
        }
    }
    
    protected function updateTracee(string $tracee, int $stamp)
    {
        $this->flushCache();
        $data = $this->getCache($tracee);
        $this->putValue($tracee, $stamp, $data->value);
    }

    protected function doGetValueAt(string $path, int $timestamp)
    {
        $values = array_reverse($this->readAllValues($path));
        foreach ($values as $entry) {
            if ($timestamp >= $entry->stamp) {
                return $entry->value;
            }
        }
        return $entry->value;
    }
    
    protected function doGetRangeValues(string $path, int $start, int $end): array
    {
        $values = $this->readAllValues($path);
        $result = [];
        
        foreach ($values as $entry) {
            if (($entry->stamp >= $start) && ($entry->stamp <= $end)) {
                $result[] = $entry;
            }
        }
        
        return $result;
    }
    
}