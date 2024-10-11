<?php
/**
 * @file SimpleStorage.php
 * A very simple storage that stores the values in an array
 * @author Klaus Dimde
 * Lang en
 * Reviewstatus: 2024-02-11
 * Localization: none
 * Documentation: unknown
 * Tests: unknown
 * Coverage: unknown
 * PSR-State: completed
 */

namespace Sunhill\Storage;

use Sunhill\Storage\Exceptions\FieldNotAvaiableException;
use Sunhill\Storage\Exceptions\FieldNotAnArrayException;

abstract class SimpleStorage extends AbstractStorage
{
    
    protected $values = [];
    
    abstract protected function readValues(): array;
        
    /**
     * Performs the retrievement of the value
     * 
     * @param string $name
     */
    protected function doGetValue(string $name)
    {
        if (!isset($this->values[$name])) {
            throw new FieldNotAvaiableException("The field '$name' is not avaiable.");
        }
        return $this->values[$name];
    }
    
    protected function doGetIndexedValue(string $name, mixed $index): mixed
    {
        if (!isset($this->values[$name])) {
            throw new FieldNotAvaiableException("The field '$name' is not avaiable.");            
        }
        if (!is_array($this->values[$name])) {
            throw new FieldNotAnArrayException("The field '$name' is not an array.");
        }
        return $this->values[$name][$index];
    }
    
    protected function doGetElementCount(string $name): int
    {
        if (!isset($this->values[$name])) {
            throw new FieldNotAvaiableException("The field '$name' is not avaiable.");
        }
        if (!is_array($this->values[$name])) {
            throw new FieldNotAnArrayException("The field '$name' is not an array.");
        }
        return count($this->values[$name]);
    }
    
    protected function doGetOffsetExists(string $name, $index): bool
    {
        return isset($this->values[$name][$index]);
    }
    
    /**
     * Prepares the retrievement of the value
     * 
     * @param string $name
     */
    protected function prepareGetValue(string $name)
    {
        if (empty($this->values)) {
            $this->values = $this->readValues();
        }
    }
    
    /**
     * Performs the setting of the value
     * 
     * @param string $name
     * @param unknown $value
     */
    protected function doSetValue(string $name, $value)
    {
        // Should not be called
    }
    
    protected function doSetIndexedValue(string $name, $index, $value)
    {
        // Should not be called
    }
    
    /**
     * Returns if this storage was modified
     *
     * @return bool
     */
    public function isDirty(): bool
    {
        return false; // Cant be dirty
    }

    protected function doGetIsInitialized(string $name): bool
    {
        return true;
    }
    
}