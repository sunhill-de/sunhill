<?php
/**
 * @file PersistentStorage.php
 * The class for storages that could be saved and loaded to or from a persistent media like a
 * database or a file
 * @author Klaus Dimde
 * Lang en
 * Reviewstatus: 2024-10-12
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

abstract class PersistentStorage extends CommonStorage
{
    
    protected $shadow = [];
    
    /**
     * Returns if the storage itself or an entry is dirty
     * 
     * {@inheritDoc}
     * @see \Sunhill\Storage\AbstractStorage::isDirty()
     */
    public function isDirty(string $name = ''): bool
    {        
        if (empty($name)) {
            return !empty($this->shadow); // The storage is dirty when there is any entry in shadow
        }
        if (!isset($this->values[$name])) { // Is this field known?
            throw new FieldNotAvaiableException("The field '$name' is not defined.");
        }
        return isset($this->shadow[$name]);
    }
    
    /**
     * Checks if the is a need of a shadow entry
     * @param string $name
     */
    private function checkShadow(string $name)
    {
        if (!isset($this->values[$name])) {
            $this->shadow[$name] = new IsDirty();
        }
        if (isset($this->shadow[$name])) {
            return; // Already in shadow, ignore it
        }
        $this->shadow[$name] = $this->values[$name];
    }
    
    private function arrayValueDifferent(string $name, $index, $new_value): bool
    {
        if (is_null($index) || !isset($this->values[$name][$index])) {
            return true; // Appending is always different
        }
        return $this->values[$name][$index] !== $new_value;
    }
    
    /**
     * Returns if there even is a difference bewtween new and old value
     * 
     * @param string $name
     * @param unknown $new_value
     * @return bool
     */
    private function valueDifferent(string $name, $new_value): bool
    {
        if (!isset($this->values[$name])) {
            return true; // When unknown field always assume different
        }
        return ($new_value !== $this->values[$name]);    
    }
    
    /**
     * Performs the setting of the value
     *
     * @param string $name
     * @param unknown $value
     */
    protected function doSetValue(string $name, $value)
    {
        if ($this->valueDifferent($name,$value)) {
            $this->checkShadow($name);
            $this->values[$name] = $value;
        }
    }
    
    protected function doSetIndexedValue(string $name, $index, $value)
    {
        if ($this->arrayValueDifferent($name, $index, $value)) {
            $this->checkShadow($name);
            if (is_null($index)) {         
                $this->values[$name][] = $value;
            } else {
                $this->values[$name][$index] = $value;
            }
        }
    }
            
    protected $structure;
    
    /**
     * Sets the structure of the owning property
     * 
     * @param array $structure
     * @wiki /PersistentStorage#Structure
     */
    public function setStructure(array $structure)
    {
        $this->structure = $structure;    
    }
    
    /**
     * Returns the values that where modified in an already loaded storage
     * 
     * @return array
     */
    protected function getModifiedValues(): array
    {
       $result = [];
       foreach ($this->shadow as $key => $value) {
           $entry = new \stdClass();
           if (!is_a($value,IsDirty::class)) {
               $entry->old = $value;
           } else {
               $entry->old = null;
           }
           $entry->new = $this->values[$key];
           $result[$key] = $entry;
       }
       return $result;
    }
    
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
    
    public function commit()
    {
        if (!$this->isDirty()) { // When not dirty then there is nothing to do
            return;
        }
        if ($this->isLoaded()) {
            $this->doCommitLoaded();
        } else {
            $this->setID($this->doCommitNew());
        }
    }
    
    /**
     * Writes any modified value (except the newly created ones) back to the original value
     * stored in $shadow.
     * 
     * {@inheritDoc}
     * @see \Sunhill\Storage\AbstractStorage::rollback()
     */
    public function rollback()
    {
        foreach ($this->shadow as $key => $value) {
            if (!is_a($value,IsDirty::class)) {
                $this->values[$key] = $value;
            }
        }
        $this->shadow = [];
    }
    
    protected function doMigrate()
    {
        // Does nothing by default
    }
    
    /**
     * Stores the current id
     * @var unknown
     */
    protected $id;
    
    /**
     * Due the fact that id could be null we need a way to determine if the storage was
     * already loaded.
     * 
     * @var boolean
     */
    protected $loaded = false;
    
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
    
    public function isLoaded(): bool
    {
        return $this->loaded;
    }
    
    /**
     * Loads the data for the entry identified by $id from the persistent storage
     * 
     * @param mixed $id, could be null if only one persistent entry exists
     */
    public function load(mixed $id = null)
    {
        if (!$this->isValidID($id)) {
            throw new InvalidIDException("The given id is not valid for this storage");
        }
        if ($this->isLoaded()) {
            throw new StorageAlreadyLoadedException("The storage was already loaded");
        }
        $this->setID($id);
        $this->doLoad($id);
        $this->loaded = true;
    }
    
    /**
     * Checks if the given id is a valid one
     * 
     * @param mixed $id
     * @return bool
     */
    abstract protected function isValidID(mixed $id): bool;
    
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
        $this->setID(null);
    }
}