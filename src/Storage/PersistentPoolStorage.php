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
 * Coverage: 86.96 % (2024-11-13)
 * PSR-State: completed
 */

namespace Sunhill\Storage;

use Sunhill\Storage\Exceptions\StorageAlreadyLoadedException;
use Sunhill\Storage\Exceptions\InvalidIDException;
use Sunhill\Query\BasicQuery;

abstract class PersistentPoolStorage extends AbstractPersistentStorage
{
  
    abstract protected function isValidID(mixed $id): bool;
    
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
            throw new InvalidIDException(getScalarMessage("The given id :variable is not valid for this storage",$id));
        }
        $this->setId($id);
        $this->doLoad($id);
        $this->loaded = true;
    }
    
    /**
     * This function returns true, when the given ID exists in the pool.
     * 
     * @param mixed $id
     * @return bool
     */
    public function IDExists(mixed $id): bool
    {
        return true; // We assume true, could be overwritten  
    }
    
    /**
     * Performs the load of data from the persitent
     * @param mixed $id
     */
    abstract protected function doLoad(mixed $id);
    
    public function delete(mixed $id = null)
    {
        if (!$this->isValidID($id)) {
            throw new InvalidIDException("The given id is not valid for this storage");
        }
        if ($this->isLoaded() and is_null($id)) {
            $id = $this->getID();
        }
        $this->doDelete($id);
        $this->setID(null);
        $this->loaded = false;
    }
    
    abstract protected function doDelete(mixed $id);
    
    /**
     * Persistent pool storages can't initiated a load because we don't know the id for sure
     *
     * {@inheritDoc}
     * @see \Sunhill\Storage\AbstractPersistentStorage::handleUnloaded()
     */
    protected function handleUnloaded()
    {
        if (!is_null($this->getID())) {
            return $this->load($this->getID()); // unlikely event
        }
  //      throw 
    }
    
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
     * 
     * @return mixed
     */
    public function getID(): mixed
    {
        return $this->id;
    }
    
    /**
     * Returns a query on this kind of pool
     * 
     * @return BasicQuery
     */
    public function query(): BasicQuery
    {
        return $this->doQuery();
    }
    
    abstract protected function doQuery(): BasicQuery;
}