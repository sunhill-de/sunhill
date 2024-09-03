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
use Illuminate\Support\Facades\Cache;
use Sunhill\Properties\Storage\Exceptions\CacheIDNotSetException;

abstract class GroupCacheStorage extends SimpleStorage
{
    
    /**
     * Prepares the retrievement of the value
     * 
     * @param string $name
     */
    protected function prepareGetValue(string $name)
    {
        if (empty($this->values)) {
            $this->values = $this->readValues();
            
            if (!$this->isCachable()) {
                throw new CacheIDNotSetException("No cache id is set in GroupCacheStorage");
            }
            foreach ($this->values as $key => $value) {
                Cache::put($this->getCacheID().'.'.$key, $value, $this->getCacheTime());
            }
                
        }
    }
        
}