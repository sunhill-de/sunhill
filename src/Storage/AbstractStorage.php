<?php
/**
 * @file AbstractStorage.php
 * The basic class for storages. While properties are responsible for the processing of data
 * a storage is responsible for accessing and storing data.
 * @author Klaus Dimde
 * Lang en
 * Reviewstatus: 2024-10-09
 * Localization: none
 * Documentation: unknown
 * Tests: unknown
 * Coverage: 100% (2024-11-13)
 * PSR-State: completed
 */

namespace Sunhill\Storage;

use Illuminate\Support\Facades\Cache;
use Sunhill\Basic\Base;
use Sunhill\Storage\Exceptions\StructureNeededException;

abstract class AbstractStorage extends Base
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
        $this->checkAccess();
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
        $this->checkAccess();
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
    
    public function clearArray(string $name)
    {
            
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
     * This method is called before any reading or writing access. Per default it does
     * nothing (so it is not abstract) but could be used to check if a storage is loaded.
     */
    protected function checkAccess()
    {
        // Do nothing by default
    }
    
    /**
     * Sets the given value
     * 
     * @param string $name
     * @param unknown $value
     */
    public function setValue(string $name, $value)
    {        
        $this->checkAccess();
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
        $this->checkAccess();
        $this->doSetIndexedValue($name, $index, $value);
        if ($this->isCachable()) {
            Cache::put($this->getCacheID().'.'.$name.'.'.$index, $value, $this->cache_time);            
        }
    }
    
    /**
     * Returns if this storage was modified
     * @param $name, default '' the name of the value that is checked to be dirty
     * if empty returns if the storage is dirty at all.
     * @return bool
     */
    public function isDirty(string $name = ''): bool
    {
        return false; // By default never dirty
    }

    
    /**
     * For cached storages performs the flush of the cache. Has to be called by property.
     */
    public function commit()
    {
        // does nothing by default
    }
    
        
    
    /**
     * For cached storages performs the reollback of the cache. Has to be called
     * by property.
     * 
     */
    public function rollback()
    {
        // does nothing by default
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
    
    protected $structure;
    
    /**
     * Sets the structure of the owning property
     *
     * @param array $structure
     * @wiki /PersistentStorage#Structure
     */
    public function setStructure(\stdClass $structure)
    {
        $this->structure = $structure;
    }
    
    /**
     * Checks if the $structures field was set. If not it raises an exception. This functions
     * should be called by doCommitXXXX() or doMigrate() when the structure is needed to perform
     * this step.
     */
    protected function structureNeeded()
    {
        if (is_null($this->structure)) {
            throw new StructureNeededException("The structure of the owning property is needed but not provided");
        }
    }
    
    
}