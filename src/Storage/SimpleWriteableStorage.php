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

namespace Sunhill\Properties\Storage;

use Sunhill\Properties\Storage\Exceptions\FieldNotAvaiableException;

abstract class SimpleWriteableStorage extends SimpleStorage
{
    
    /**
     * Returns if this property is writeable
     * @param string $name
     * @return bool
     */
    public function getIsWriteable(string $name): bool
    {
        return true;  
    }
    
    /**
     * Performs the setting of the value
     * 
     * @param string $name
     * @param unknown $value
     */
    protected function doSetValue(string $name, $value)
    {
        $this->values[$name] = $value;
    }
    
    protected function doGetIsInitialized(string $name): bool
    {
        return isset($this->values[$name]);
    }
    
    protected function doSetIndexedValue(string $name, $index, $value)
    {
        if (!isset($this->values[$name])) {
            $this->values[$name] = [];
        }
        if (is_null($index)) {
            $this->values[$name][] = $value;
        } else {
            $this->values[$name][$index] = $value;            
        }
    }
    
}