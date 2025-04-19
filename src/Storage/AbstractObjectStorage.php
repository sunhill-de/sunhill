<?php
/**
 * @file PersistentSubPooledStorage.php
 * This class defines the standard expected behavior of a PersistentPoolStorage:
 * - ID is always an integer
 * - arrays are stores in subpools named like storage_id + _ + field_name
 * - associated tags are stored under a storage_id named tagobjectassigns
 * - associated attributes are stored under a storage_id named attributeobjectassigns
 * 
 * @author Klaus Dimde
 * Lang en
 * Reviewstatus: 2025-04-17
 * Creation date: 2025-04-17
 * Localization: none
 * Documentation: unknown
 * Tests: 
 * Coverage: 
 */

namespace Sunhill\Storage;

use Sunhill\Facades\Properties;

abstract class AbstractObjectStorage extends PersistentPoolStorage
{
  
    /**
     * Returns all distinct storage sub ids.
     * @return array
     */
    protected function getStorageSubids(): array
    {
        $result = [];
        foreach ($this->structure->elements as $entry) {
            if (!in_array($entry->storage_subid,$result)) {
                $result[] = $entry->storage_subid;
            }
        }
        return $result;
    }
 
    /**
     * Returns all field that are arrays
     * @return array
     */
    protected function getArrays(): array
    {
        $result = [];
        foreach ($this->structure->elements as $entry) {
            if ($entry->type == 'array') {
                $result[] = $entry;
            }
        }
        return $result;
    }
        
    /**
     * Checks if the given id is a valid type (in this case an integer)
     * 
     * {@inheritDoc}
     * @see \Sunhill\Storage\PersistentPoolStorage::isValidID()
     */
    protected function isValidID(mixed $id): bool
    {
        return (is_int($id));
    }
    
    /**
     * Updates the storage with the subid. It uses $key to identiy the record(s) and sets the givenvalues
     *  
     * @param unknown $key
     * @param unknown $values
     */
    abstract protected function updateStorageSubid(string $subid, int $key, array $values, string $key_field = 'id');
    
    /**
     * Deletes all references to key from the given
     *  
     * @param unknown $key
     */
    abstract protected function deleteStorageSubid(string $subid, int $key, string $key_field = 'id');
    
    /**
     * Inserts into the given storage subid the given values. If value is a array of arrays then insert every entry as a separate record
     * 
     * @param string $subid
     * @param array $values
     */
    abstract protected function insertStorageSubid(string $subid, array $values);

    /**
     * Loads from the given storage subif the values with the given key
     * 
     * @param string $subid
     * @param int $key
     * @return array
     */
    abstract protected function loadStorageSubid(string $subid, int $key, string $key_field = 'id'): array|\stdClass;
    
    private function commitLoadedObjects()
    {
        
    }
    
    private function commitLoadedClasses()
    {
        
    }
    
    private function commitLoadedArrays()
    {
        
    }
    
    private function commitLoadedTags()
    {
        
    }
    
    private function commitLoadedAttributes()
    {
        
    }
    
    /**
     * Performs the commit of a existing entry, meaning transfering the data to the
     * persistent medium and overwriting the previously stored.
     *
     * @wiki /PersistentStorage
     */
    protected function doCommitLoaded()
    {
        $this->commitLoadedObjects();
        $this->commitLoadedClasses();
        $this->commitLoadedArrays();
        $this->commitLoadedTags();
        $this->commitLoadedAttributes();
    }
    
    private function commitNewObjects()
    {
        
    }
    
    private function commitNewClasses()
    {
        
    }
    
    private function commitNewArrays()
    {
        
    }
    
    private function commitNewTags()
    {
        
    }
    
    private function commitNewAttributes()
    {
        
    }

    /**
     * Performs the commit of a new entry, meaning creating a new entry on the persistent
     * medium and setting the id.
     *
     *  @wiki /PersistentStorage
     */
    protected function doCommitNew()
    {
        $this->commitNewObjects();
        $this->commitNewClasses();
        $this->commitNewArrays();
        $this->commitNewTags();
        $this->commitNewAttributes();        
    }
        
    private function loadClasses()
    {
        $subids = $this->getStorageSubids();
        foreach ($subids as $subid) {
            $data = $this->loadStorageSubid($subid, $this->getID());
            foreach ($data as $key => $value) {
                $this->values[$key] = $value;
            }
        }
    }
    
    private function loadArrays()
    {
        $array_fields = $this->getArrays();
        foreach ($array_fields as $field) {
           $table_name = $field->storage_subid.'_'.$field->name;
           $data = $this->loadStorageSubid($table_name, $this->getID(), 'container_id');
           foreach ($data as $record) {
               $this->values[$field->name][$record->index] = $record->element;               
           }
        }
    }
    
    private function loadTags()
    {
        $data = $this->loadStorageSubid('tagobjectassigns', $this->getID(), 'container_id');
        $this->values['_tags'] = [];
        foreach ($data as $entry) {
            $this->values['_tags'][] = $entry->tag_id;
        }            
    }
    
    private function loadAttributes()
    {
        $data = $this->loadStorageSubid('attributeobjectassigns', $this->getID(), 'container_id');
        $this->values['_attributes'] = [];
        foreach ($data as $entry) {
            $attribute = Properties::loadAttribute($this->getID(), $entry->attribute_id);
            $key = array_keys($attribute)[0];
            $value = array_values($attribute)[0];
            $this->values['_attributes'][$key] = $value;
        }
    }

    /**
     * Performs the load of data from the persitent
     * @param mixed $id
     */
    protected function doLoad(mixed $id)
    {
        $this->loadClasses();
        $this->loadArrays();
        $this->loadTags();
        $this->loadAttributes();        
    }
    
    private function deleteObjects()
    {
        
    }
    
    private function deleteClasses()
    {
        
    }
    
    private function deleteArrays()
    {
        
    }
    
    private function deleteTags()
    {
        
    }
    
    private function deleteAttributes()
    {
        
    }
    
    protected function doDelete(mixed $id)
    {
        $this->deleteObjects();
        $this->deleteClasses();
        $this->deleteArrays();
        $this->deleteTags();
        $this->deleteAttributes();        
    }
    
}