<?php
/**
 * @file PersistentSingleStorage.php
 * The class for storages that could be saved and loaded to or from a persistent media like a
 * database or a file. This class only represents one single property not a pool of properties.
 * 
 * @author Klaus Dimde
 * Lang en
 * Reviewstatus: 2024-10-17
 * Localization: none
 * Documentation: unknown
 * Tests: unknown
 * Coverage: unknown
 * PSR-State: completed
 */

namespace Sunhill\Storage;

use Sunhill\Storage\Exceptions\InvalidIDException;
use Sunhill\Storage\Exceptions\StorageAlreadyLoadedException;
use Sunhill\Storage\Exceptions\FieldNotAvaiableException;
use Sunhill\Storage\Exceptions\StructureNeededException;

abstract class PersistentSingleStorage extends AbstractPersistentStorage
{
    
    
    /**
     * Loads the data 
     * 
     */
    public function load()
    {
        if ($this->isLoaded()) {
            throw new StorageAlreadyLoadedException("The storage was already loaded");
        }
        $this->doLoad();
        $this->loaded = true;
    }
    
    /**
     * Performs the load of data from the persitent 
     * @param mixed $id
     */
    abstract protected function doLoad();
    
    /**
     * Loading a storage when already loaded with data is forbidden. This resets 
     */
    public function reset()
    {
        $this->values = [];
        $this->loaded = false;
    }
}