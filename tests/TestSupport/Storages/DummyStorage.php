<?php

namespace Sunhill\Tests\TestSupport\Storages;

use Sunhill\Storage\AbstractStorage;

class DummyStorage extends AbstractStorage
{
    
    public static $preloaded_values = ['keyA'=>A,'keyB'=>3.56,'KeyC'=>[1,2,3]];
    
    protected $values = [];
    
    protected $shadow = [];
    
    protected function checkInitialize()
    {
        if (empty($this->values)) {
            $this->values = static::$preloaded_values;
        }
    }
    
    protected function doGetIsInitialized(string $name): bool
    {
        $this->checkInitialize();
        return isset($this->values[$name]);
    }
    
    protected function doGetValue(string $name)
    {
        return $this->values[$name];
    }
    
    protected function doGetIndexedValue($name, mixed $index): mixed
    {
        if (isset($this->values[$name]) && is_array($this->values[$name])) {
            return $this->values[$name][$index];
        }
    }
    
    protected function doGetElementCount(string $name): int
    {
        if (isset($this->values[$name]) && is_array($this->values[$name])) {
            return count($this->values[$name]);
        }
    }
    
    protected function doGetOffsetExists(string $name, $index): bool
    {
        if (isset($this->values[$name]) && is_array($this->values[$name])) {
            return isset($this->values[$name][$index]);
        }        
    }
    
    protected function doSetValue(string $name,$value)
    {
        if (isset($this->values[$name]) && !isset($this->shadow[$name])) {
            $this->shadow[$name] = $this->values[$name];
        }
        $this->values[$name] = $value;
    }
    
    protected function doSetIndexedValue($name, $index, $value)
    {
        if (isset($this->values[$name]) && !isset($this->shadow[$name])) {
            $this->shadow[$name] = $this->values[$name];
        }
        if (!isset($this->values[$name])) {
            $this->values[$name] = [$index=>$value];
        } else {
            $this->values[$name][$index] = $value;
        }
    }
    
    protected function doRollback()
    {
        foreach ($this->shadow as $key => $value) {
            $this->values[$key] = $value;
        }
    }
    
    protected function doCommit()
    {
        static::$preloaded_values = $this->values;
    }
    
    public function isDirty(): bool
    {
        return !is_empty($this->shadow);
    }
    
}
