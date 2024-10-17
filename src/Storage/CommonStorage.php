<?php
/**
 * @file CommonStorage.php
 * A very simple storage that returns values that are stored internally in an array
 * @author Klaus Dimde
 * Lang en
 * Reviewstatus: 2024-10-12
 * Localization: none
 * Documentation: unknown
 * Tests: unknown
 * Coverage: 66.67% (2ß24-10-17)
 * PSR-State: completed
 */

namespace Sunhill\Storage;

use Sunhill\Storage\Exceptions\FieldNotAvaiableException;
use Sunhill\Storage\Exceptions\FieldNotAnArrayException;

abstract class CommonStorage extends AbstractStorage
{
    protected $values = [];
    
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
        
    protected function doGetIsInitialized(string $name): bool
    {
        return isset($this->values[$name]);
    }
    
}