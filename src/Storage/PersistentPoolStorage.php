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
 * Coverage Unit: 96.67 (2025-06-06)
 * PSR-State: completed
 */

namespace Sunhill\Storage;

use Sunhill\Storage\Exceptions\StorageAlreadyLoadedException;
use Sunhill\Storage\Exceptions\InvalidIDException;
use Sunhill\Storage\Exceptions\IDNotFoundException;
use Sunhill\Query\QueryParser\Nodes\QueryNode;

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
     * Checks if the storage is already loaded. If yes, it raises an exception (storages have to
     * be resetted by reset() first)
     * 
     */
    private function checkAlreadyLoaded()
    {
        if ($this->isLoaded()) {
            throw new StorageAlreadyLoadedException("The storage was already loaded");
        }        
    }
    
    /**
     * Checks if the if is accepted by this storage. If not throws an exception
     * 
     * @param unknown $id
     */
    protected function checkIsValidID($id)
    {
        if (!$this->isValidID($id)) {
            throw new InvalidIDException(getScalarMessage("The given id :variable is not valid for this storage",$id));
        }        
    }
    
    /**
     * Checks if the id exists. If not throws an exception
     * 
     * @param unknown $id
     */
    protected function checkIDexists($id)
    {
        if (!$this->IDExists($id)) {
            throw new IDNotFoundException("The id '$id' was not found.");
        }        
    }
    
    /**
     * Loads the data
     *
     */
    public function load(mixed $id)
    {
        $this->checkAlreadyLoaded();
        $this->checkIsValidID($id);
        $this->checkIDexists($id);
        
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
        if ($this->isLoaded() and is_null($id)) {
            $id = $this->getID();
        }
        $this->checkIsValidID($id);
        $this->checkIDexists($id);
        
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
    
    abstract protected function doExecuteQuery(QueryNode $node);
    
    public function executeQuery(QueryNode $node)
    {
        return $this->doExecuteQuery($node);    
    }
    
}