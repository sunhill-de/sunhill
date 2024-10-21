<?php
/**
 * @file PersistentPoolStorage.php
 * The class for storages that could be saved and loaded to or from a persistent media pool like a
 * database or a file with entries of the same type. The storage has to be identified by any kind 
 * of id.
 * 
 * @author Klaus Dimde
 * Lang en
 * Reviewstatus: 2024-10-17
 * Localization: none
 * Documentation: unknown
 * Tests: unknown
 * Coverage: 100 % (2024-10-17)
 * PSR-State: completed
 */

namespace Sunhill\Storage;

use Sunhill\Storage\Exceptions\StorageAlreadyLoadedException;
use Sunhill\Storage\Exceptions\InvalidIDException;

abstract class PersistentPoolStorage extends AbstractPersistentStorage
{
  
    /**
     * Performs the commit of a existing entry, meaning transfering the data to the
     * persistent medium and overwriting the previously stored.
     *
     * @wiki /PersistentStorage
     */
    abstract protected function doCommitLoaded();
    
    /**
     * Performs the commit of a new entry, meaning creating a new entry on the persistent
     * medium and setting the id.
     *
     *  @wiki /PersistentStorage
     */
    abstract protected function doCommitNew();
    
    protected function doCommit()
    {
        if ($this->isLoaded()) {
            $this->doCommitLoaded();
        } else {
            $this->setID($this->doCommitNew());
        }
    }
    
    /**
     * Loads the data
     *
     */
    public function load(mixed $id)
    {
        if ($this->isLoaded()) {
            throw new StorageAlreadyLoadedException("The storage was already loaded");
        }
        if (!$this->isValidID($id)) {
            throw new InvalidIDException("The given id is not valid for this storage");
        }
        $this->setId($id);
        $this->doLoad($id);
        $this->loaded = true;
    }
    
    /**
     * Performs the load of data from the persitent
     * @param mixed $id
     */
    abstract protected function doLoad(mixed $id);
    
    /**
     * Loading a storage when already loaded with data is forbidden. This resets
     */
    public function reset()
    {
        $this->values = [];
        $this->loaded = false;
    }
        
    /**
     * Stores the current id
     * @var unknown
     */
    protected $id;
        
    /**
     * Sets the current id
     * @param mixed $id
     */
    protected function setID(mixed $id)
    {
        $this->id = $id;
    }
    
    /**
     * Returns the current id
     * @return mixed
     */
    public function getID(): mixed
    {
        return $this->id;
    }
    
    
}