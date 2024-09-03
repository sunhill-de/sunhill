<?php
/**
 * @file AbstractStorage.php
 * The basic class for storages. While properties are responsible for the processing of data
 * a storage is responsible for accessing and storing data.
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

use Illuminate\Support\Facades\Cache;

abstract class AbstractStorage
{
    
    /**
     * The cache id is an (optional) prefix for the caching mechanism. If set all read and write actions
     * go through the cache.
     * 
     * @var string
     */
    protected $cache_id = '';
    
    /**
     * Stores how long the values should be cached (if at all as defined above)
     * 
     * @var integer
     */
    protected $cache_time = 60; // 1 Minute
    
    /**
     * Sets an cache id. This can be done from external or in the storage itself for example in the 
     * constructor 
     * 
     * @param string $id
     * @return self
     */
    public function setCacheID(string $id): self
    {
        $this->cache_id = $id;
        return $this;
    }
    
    /**
     * Returns the cache id
     * 
     * @return string
     */
    public function getCacheID(): string
    {
        return $this->cache_id;    
    }
    
    /**
     * Sets the current caching time
     * 
     * @param int $int
     * @return self
     */
    public function setCacheTime(int $int): self
    {
        $this->cache_time = $int;
        return $this;
    }
    
    /**
     * Returns the current caching time
     * 
     * @return int
     */
    public function getCacheTime(): int
    {
        return $this->cache_time;    
    }
    
    /**
     * Returns if this storage is cachable at all.
     * 
     * @return bool
     */
    public function isCachable(): bool
    {
        return !empty($this->cache_id);
    }
    
    /**
     * Returns the required read capability or null if there is none
     * 
     * @param string $name
     * @return string
     */
    abstract public function getReadCapability(string $name): ?string;
    
    /**
     * Returns if the property is readable
     * 
     * @param string $name
     * @return bool
     */
    abstract public function getIsReadable(string $name): bool;
    
    /**
     * Performs the retrievement of the value
     * 
     * @param string $name
     */
    abstract protected function doGetValue(string $name);
    
    /**
     * Prepares the retrievement of the value
     * 
     * @param string $name
     */
    protected function prepareGetValue(string $name)
    {
        
    }

    protected function searchCache(string $cache_name)
    {
        if ($this->isCachable()) {
            if (Cache::has($this->getCacheID().'.'.$cache_name)) {
                return Cache::get($this->getCacheID().'.'.$cache_name);
            }
        }
        return false;
    }
    
    /**
     * Gets the given value
     * 
     * @param string $name
     * @return unknown
     */
    public function getValue(string $name)
    {
        if ($value = $this->searchCache($name)) {
            return $value;
        }
        $this->prepareGetValue($name);
        $value = $this->doGetValue($name);
        if ($this->isCachable()) {
            Cache::put($this->getCacheID().'.'.$name, $value, $this->cache_time);
        }
        return $value;
    }
 
    /**
     * Gets from the array element $name the entry identified by $index
     * This routine does not check, if the element is an array at all. This has do be done by
     * the owning property
     * 
     * @param string $name
     * @param mixed $index integer or string
     * @return mixed
     */
    abstract protected function doGetIndexedValue(string $name, mixed $index): mixed;
    
    /**
     * Gets from the array element $name the count of entries
     * This routine does not check, if the element is an array at all. This has do be done by
     * the owning property
     * 
     * @param string $name
     * @return int
     */
    abstract protected function doGetElementCount(string $name): int;

    /**
     * Gets from the array element $name the entry identified by $index
     * This routine does not check, if the element is an array at all. This has do be done by
     * the owning property. This routine does check, if the entry is already cached
     * 
     * @param string $name
     * @param mixed $index
     * @return boolean|mixed|mixed
     */
    public function getIndexedValue(string $name, mixed $index)
    {
        if ($value = $this->searchCache($name.'.'.$index)) {
            return $value;
        }
        $this->prepareGetValue($name);
        $value = $this->doGetIndexedValue($name, $index);
        if ($this->isCachable()) {
            Cache::put($this->getCacheID().'.'.$name.'.'.$index, $value, $this->cache_time);            
        }
        return $value;
    }
    
    /**
     * Returns how many entries the array element has.
     * Note: This routine does not check if it is an array at all, this has to be done on a higher level
     * 
     * @param string $name
     * @return int
     */
    public function getElementCount(string $name): int
    {
        $this->prepareGetValue($name);
        return $this->doGetElementCount($name);    
    }
    
    abstract protected function doGetOffsetExists(string $name, $index): bool; 
    
    /**
     * Returns if the entry with the given index exists
     * Note: This routine does not check if it is an array at all, this has to be done on a higher level
     *
     * @param string $name
     * @return int
     */
    public function getOffsetExists(string $name, $index): bool
    {
        $this->prepareGetValue($name);
        return $this->doGetOffsetExists($name, $index);
    }
    
    protected function doGetKeys(string $name): array
    {
        $result = [];
        $index = 0;
        while ($index < $this->getElementCount($name)) {
            $result[] = $index++;
        }
        return $result;
    }
    
    public function getKeys(string $name): array
    {
        $this->prepareGetValue($name);
        return $this->doGetKeys($name);        
    }
    
    /**
     * Returns the required write capability or null if there is none
     * 
     * @param string $name
     * @return string|NULL
     */
    abstract public function getWriteCapability(string $name): ?string;
    
    /**
     * Returns if this property is writeable
     * @param string $name
     * @return bool
     */
    abstract public function getIsWriteable(string $name): bool;
    
    /**
     * Returns the modify capability or null if there is none
     * 
     * @param string $name
     * @return string|NULL
     */
    abstract public function getModifyCapability(string $name): ?string;
        
    /**
     * Performs the setting of the value
     * 
     * @param string $name
     * @param unknown $value
     */
    abstract protected function doSetValue(string $name, $value);
    
    /**
     * Sets in the array property $name the element identified by $index with $value
     *
     * @param string $name
     * @param unknown $index
     * @param unknown $value
     */
    abstract protected function doSetIndexedValue(string $name, $index, $value);
    
    /**
     * Perfoms action after setting the value
     * 
     * @param string $name
     * @param unknown $value
     */
    protected function postprocessSetValue(string $name, $value)
    {
        
    }
    
    /**
     * Sets the given value
     * 
     * @param string $name
     * @param unknown $value
     */
    public function setValue(string $name, $value)
    {        
        $this->doSetValue($name, $value);
        if ($this->isCachable()) {
            Cache::put($this->getCacheID().'.'.$name, $value, $this->cache_time);
        }
        $this->postprocessSetValue($name, $value);
    }

    /**
     * Sets in the array property $name the element identified by $index with $value
     * 
     * @param string $name
     * @param unknown $index
     * @param unknown $value
     */
    public function setIndexedValue(string $name, $index, $value)
    {
        $this->doSetIndexedValue($name, $index, $value);    
    }
    
    /**
     * Returns if this storage was modified
     *
     * @return bool
     */
    abstract public function isDirty(): bool;

    /**
     * For cached storages performs the flush of the cache. Has to be called by property.
     */
    public function commit()
    {
        
    }
    
    /**
     * For cached storages performs the reollback of the cache. Has to be called
     * by property.
     * 
     */
    public function rollback()
    {
        
    }

    abstract protected function doGetIsInitialized(string $name): bool;
    
    /**
     * Returns if the value was already initialized with a value
     * 
     * @return bool
     */
    public function getIsInitialized(string $name): bool
    {
        $this->prepareGetValue($name);
        return $this->doGetIsInitialized($name);
    }
    
}