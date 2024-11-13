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
 * Coverage: 100% (2ß24-11-13)
 * PSR-State: completed
 */

namespace Sunhill\Storage;

use Sunhill\Storage\Exceptions\FieldNotAvaiableException;
use Sunhill\Storage\Exceptions\FieldNotAnArrayException;
use Sunhill\Properties\Exceptions\InvalidIndexException;

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
        $this->checkFieldExistence($name);
        return $this->values[$name];
    }

    private function checkFieldExistence(string $name)
    {
        if (!isset($this->values[$name])) {
            throw new FieldNotAvaiableException("The field '$name' is not avaiable.");
        }       
    }
    
    private function checkFieldIsArray(string $name)
    {
        if (!is_array($this->values[$name])) {
            throw new FieldNotAnArrayException("The field '$name' is not an array.");
        }        
    }
    
    protected function doGetIndexedValue(string $name, mixed $index): mixed
    {
        $this->checkFieldExistence($name);
        $this->checkFieldIsArray($name);
        if (!isset($this->values[$name][$index])) {
            throw new InvalidIndexException("The index does not exist.");
        }
        return $this->values[$name][$index];
    }
    
    protected function doGetElementCount(string $name): int
    {
        $this->checkFieldExistence($name);
        $this->checkFieldIsArray($name);
        return count($this->values[$name]);
    }
    
    protected function doGetOffsetExists(string $name, $index): bool
    {
        $this->checkFieldExistence($name);
        $this->checkFieldIsArray($name);
        return isset($this->values[$name][$index]);
    }
        
    protected function doGetIsInitialized(string $name): bool
    {
        return isset($this->values[$name]);
    }
    
}