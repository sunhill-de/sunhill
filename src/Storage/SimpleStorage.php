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
 * Coverage: 40 % (2024-10-17)
 * PSR-State: completed
 */

namespace Sunhill\Storage;

abstract class SimpleStorage extends CommonStorage
{
    
    abstract protected function readValues(): array;
        
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
    
    protected function doGetIsInitialized(string $name): bool
    {
        return true;
    }
    
}