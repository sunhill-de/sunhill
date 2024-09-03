<?php

namespace Sunhill\Properties\Tests\TestSupport\Storages;

use Sunhill\Properties\Storage\AbstractStorage;

class TestAbstractStorage extends AbstractStorage
{
    public $values = ['test'=>'TESTVALUE','array_val'=>['ABC','DEF']];
    
    public function getReadCapability(string $name): ?string
    {
        return null; // No need to test
    }
    
    public function getIsReadable(string $name): bool
    {
        return true;
    }
    
    protected function doGetValue(string $name)
    {
        return $this->values[$name];
    }
    
    protected function doGetIndexedValue(string $name, mixed $index): mixed
    {
        return $this->values[$name][$index];    
    }
    
    protected function doGetElementCount(string $name): int
    {
        return count($this->values[$name]);    
    }
    
    public function getWriteCapability(string $name): ?string
    {
        return null;
    }
    
    public function getWriteable(string $name): bool
    {
        return true;
    }
    
    public function getModifyCapability(string $name): ?string
    {
        return null;
    }
    
    protected function doGetOffsetExists(string $name, $index): bool
    {
        return isset($this->values[$name][$index]);
    }
    
    public function getIsWriteable(string $name): bool
    {
        return true;
    }
    
    protected function doGetIsInitialized(string $name): bool
    {
        return true;
    }
    protected function doSetValue(string $name, $value)
    {
        $this->values[$name] = $value;
    }
    
    protected function doSetIndexedValue(string $name, $index, $value)
    {
        if (is_null($index)) {
            $this->values[$name][] = $value;
        } else {
            $this->values[$name][$index] = $value;
        }
    }
    
    public function isDirty(): bool
    {
        return false;
    }
    
}

