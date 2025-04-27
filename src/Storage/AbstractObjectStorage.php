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
     * Returns all field that belong to the given subid
     * 
     * @param string $name
     * @return unknown[]
     */
    protected function getFieldsOf(string $name)
    {
        $result = [];
        foreach ($this->structure->elements as $entry) {
            if ($entry->storage_subid == $name) {
                $result[] = $entry;
            }
        }
        return $result;
    }
    
    /**
     * Returns alls fields that belong to the given subid and are not arrays
     * 
     * @param string $name
     * @return unknown[]
     */
    protected function getSimpleFieldsOf(string $name)
    {
        $result = [];
        foreach ($this->structure->elements as $entry) {
            if (($entry->storage_subid == $name) && ($entry->type !== 'array')) {
                $result[] = $entry;
            }
        }
        return $result;        
    }

    protected function assembleValues(array $fields): array
    {
        $result = [];
        foreach ($fields as $field) {
            $result[$field->name] = $this->values[$field->name];
        }
        return $result;
    }
    
    /**
     * Returns all array fields that belong to the given subid
     * 
     * @param string $table
     * @return array
     */
    protected function getArraysOf(string $table): array
    {
        $result = [];
        foreach ($this->structure->elements as $entry) {
            if (($entry->storage_subid == $table) && ($entry->type == 'array')) {
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

    abstract protected function insertObjects(array $values): int;
    
    /**
     * Loads from the given storage subif the values with the given key
     * 
     * @param string $subid
     * @param int $key
     * @return array
     */
    abstract protected function loadStorageSubid(string $subid, int $key, string $key_field = 'id'): array|\stdClass;
    
    private function getDirtyFieldsOf(string $storage_subid)
    {
        $result = [];
        $fields = $this->getFieldsOf($storage_subid);
        foreach ($fields as $field) {
            if ($this->isDirty($field->name)) {
                $result[$field->name] = $this->values[$field->name];
            }
        }
        return $result;
    }
    
    private function commitLoadedObjects()
    {
        $dirty = $this->getDirtyFieldsOf('objects');
        if (!empty($dirty)) {
            $this->updateStorageSubid('objects', $this->getID(), $dirty);
        }
    }
    
    private function commitLoadedClasses()
    {
        $subids = $this->getStorageSubids();
        foreach ($subids as $subid) {
            if ($subid == 'objects') {
                continue;
            }
            $values = $this->getDirtyFieldsOf($subid);
            if (!empty($values)) {
                $this->updateStorageSubid($subid, $this->getID(), $values);
            }
        }        
    }
    
    private function updateArray($field)
    {
        $table_name = $field->storage_subid.'_'.$field->name;
        $this->deleteStorageSubid($table_name, $this->getID(),'container_id');
        if (!empty($this->values[$field->name])) {
            $this->insertStorageSubid($table_name, $this->assmbleArrayValues($this->values[$field->name]));
        }        
    }
    
    private function commitLoadedArrays()
    {
        $array_fields = $this->getArrays();
        foreach ($array_fields as $array)
        {
            if ($this->isDirty($array->name)) {
                $this->updateArray($array);
            }
        }
    }
    
    private function commitLoadedTags()
    {
        if ($this->isDirty('_tags')) {
            $this->deleteStorageSubid('tagobjectassigns',$this->getID(),'container_id');
            if (!empty($this->values['_tags'])) {
                $this->commitNewTags();
            }
        }
    }
    
    private function commitAttribute(string $attribute, $value)
    {
        $attribute_id = Properties::getAttributeID($attribute);
        $attributes = ['container_id'=>$this->getID(), 'attribute_id'=>$attribute_id];
        Properties::storeAttribute($attribute_id, $this->getID(), $value);
        $this->insertStorageSubid('attributeobjectassigns', $attributes);
    }
    
    private function commitLoadedAttributes()
    {
        if ($this->isDirty('_attributes')) {
            $this->deleteStorageSubid('attributeobjectassigns',$this->getID(),'container_id');
            if (!empty($this->values['_attributes'])) {
                foreach ($this->values['_attributes'] as $attribute => $value) {
                    $this->commitAttribute($attribute, $value);
                }
            }
        }
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
    
    private function commitNewObject()
    {
        $this->insertObjects($this->assembleValues($this->getSimpleFieldsOf('objects')));        
    }
    
    private function commitNewClasses()
    {
        $subids = $this->getStorageSubids();
        foreach ($subids as $subid) {
            if ($subid == 'objects') {
                continue;
            }
            $values = $this->assembleValues($this->getSimpleFieldsOf($subid));
            $values['id'] = $this->getID();
            $this->insertStorageSubid($subid, $values);
        }
    }
    
    private function assmbleArrayValues(array $values)
    {
        $result = [];
        foreach ($values as $key => $value) {
            $result[] = ['container_id'=>$this->getID(),'index'=>$key,'element'=>$value];
        }
        return $result;
    }
    
    private function commitNewArrays()
    {
        $array_fields = $this->getArrays();
        foreach ($array_fields as $field) {
            $table_name = $field->storage_subid.'_'.$field->name;
            if (!empty($this->values[$field->name])) {
                $this->insertStorageSubid($table_name, $this->assmbleArrayValues($this->values[$field->name]));
            }
        }
    }
    
    private function commitNewTags()
    {
        $tags = [];
        foreach ($this->values['_tags'] as $tag) {
            $tags[] = ['container_id'=>$this->getID(),'tag_id'=>$tag];
        }
        $this->insertStorageSubid('tagobjectassigns',$tags);
    }
    
    private function commitNewAttributes()
    {
         $attributes = [];
         foreach ($this->values['_attributes'] as $name => $value) {
             $attribute_id = Properties::getAttributeID($name);
             $attributes[] = ['container_id'=>$this->getID(), 'attribute_id'=>$attribute_id];
             Properties::storeAttribute($attribute_id, $this->getID(), $value);
         }
         $this->insertStorageSubid('attributeobjectassigns', $attributes);
    }

    /**
     * Performs the commit of a new entry, meaning creating a new entry on the persistent
     * medium and setting the id.
     *
     *  @wiki /PersistentStorage
     */
    protected function doCommitNew()
    {
        $this->commitNewObject();
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
            foreach ($data[0] as $key => $value) { // We expect excactly one entry
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
            $attribute = Properties::loadAttribute($entry->attribute_id, $this->getID());
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
    
    private function deleteObjects(int $id)
    {
        $this->deleteStorageSubid('objects',$id); 
    }
    
    private function deleteClasses(int $id)
    {
        $subids = $this->getStorageSubids();
        foreach ($subids as $subid) {
            $this->deleteStorageSubid($subid, $id);
        }        
    }
    
    private function deleteArrays(int $id)
    {
        $array_fields = $this->getArrays();
        foreach ($array_fields as $field) {
            $table_name = $field->storage_subid.'_'.$field->name;
            $this->deleteStorageSubid($table_name, $id, 'container_id');
        }        
    }
    
    private function deleteTags(int $id)
    {
        $this->deleteStorageSubid('tagobjectassigns', $id, 'container_id');
    }
    
    private function deleteAttributes(int $id)
    {
        $data = $this->loadStorageSubid('attributeobjectassigns', $id, 'container_id');
        foreach ($data as $entry) {
            Properties::unsetAttribute($entry->attribute_id,$id);
        }
        $this->deleteStorageSubid('attributeobjectassigns', $id, 'container_id');
    }
    
    protected function doDelete(mixed $id)
    {
        $this->deleteObjects($id);
        $this->deleteClasses($id);
        $this->deleteArrays($id);
        $this->deleteTags($id);
        $this->deleteAttributes($id);        
    }

    /**
     * Tests if the structure only exists of '*' or '€'- If yes, it returns. If not it traverses all keys of structure and 
     * creates an empty array for it.
     * 
     * @param unknown $result
     * @param unknown $structure
     */
    private function traverseStructure(&$result, $structure)
    {
        if (isset($structure->{0})) {
            return;
        }
        foreach ($structure as $key => $value) {
            if (!isset($result->key)) {
                $result->$key = new \stdClass();
                $result->$key->given = new \stdClass();
                $result->$key->new = new \stdClass();
            }
        }
    }
    
    private function checkAttributes(&$key, $given_key, $expected_key)
    {
        if (isset($given_key->{0}) && ($given_key->{0} == '*')) {
            return; // Do nothing
        }
        foreach ($given_key as $attribute_key => $attribute_value) {
            if (!isset($expected_key->$attribute_key)) {
                $key->given->$attribute_key = $attribute_value;
            } else if (($given_key->$attribute_key !== '*') && ($given_key->$attribute_key !== $expected_key->$attribute_key)) {
                $key->given->$attribute_key = $given_key->$attribute_key;
                $key->new->$attribute_key = $expected_key->$attribute_key;
            }
        }
    }
    
    private function checkGivenStructure(&$result, $given_structure, $expected_structure)
    {
        foreach ($given_structure as $key => $value) {
            if ((!isset($expected_structure->$key))) {
                $result->$key->given = $value;
            } else {
                $this->checkAttributes($result->$key, $given_structure->$key, $expected_structure->$key);
            }
        }
    }
    
    private function checkExpectedStructure(&$result, $given_structure, $expected_structure)
    {
        foreach ($expected_structure as $key => $value) {
            if (($given_structure == ['€']) || (!isset($given_structure->$key))) {
                $result->$key->new = $value;
            }
        }        
    }
    
    public function getStructureDiff($given_structure, $expected_structure)
    {
        $result = new \stdClass();
        $this->traverseStructure($result, $given_structure);
        $this->traverseStructure($result, $expected_structure);
        if (isset($given_structure->{0}) && ($given_structure->{0} == '*')) {
            return $result; // We have a joker just return
        }
        if (!isset($given_structure->{0}) || ($given_structure->{0} !== '€')) {            
            $this->checkGivenStructure($result, $given_structure, $expected_structure);
        } else {
            foreach ($result as $key => $value) {
                $result->$key->given = new \stdClass();
                $result->$key->given->{0} = '€';
            }
        }
        $this->checkExpectedStructure($result, $given_structure, $expected_structure);
        return $result;
    }

    public function assembleStructure(string $storage_subid)
    {
        $result = new \stdClass();
        foreach ($this->structure->elements as $name => $field) {
            if ($field->storage_subid !== $storage_subid) {
                continue;
            }
            $result->$name = new \stdClass();
            $result->$name->type = $field->type;
            switch ($field->type) {
                case 'string':
                    $result->$name->max_len = $field->max_length;
                    break;
                case 'array':
                    $result->$name->index_type = $field->index_type;
                    $result->$name->element_type = $field->element_type;
                    break;
            }
        }
        return $result;
    }
    
    protected function getCurrentStructure(string $storage_subid)
    {
        
    }
    
    private function migrateStorageObjectSubid(string $storage_subid)
    {
        if ($this->storageSubidExists($storage_subid)) {
            $this->migrateUpdateStorageSubid($storage_subid, $this->getStructureDiff($this->getCurrentStructure($storage_subid),$this->assembleStructure($storage_subid)));
        } else {
            $this->migrateFreshStorageSubid($storage_subid, $this->assembleStructure($storage_subid));
        }
    }
    
    private function migrateStorageArraySubid(string $storage_subid)
    {
        
    }
    
    private function migrateClasses()
    {
        $subids = $this->getStorageSubids();
        foreach ($subids as $subid) {
            if ($subid !== 'objects') {
                $this->migrateStorageObjectSubid($subid);
            }
        }
    }
    
    private function migrateArrays()
    {
        $array_fields = $this->getArrays();
        foreach ($array_fields as $field) {
            $table_name = $field->storage_subid.'_'.$field->name;
            $this->migrateStorageSubid($table_name);
        }
    }
    public function migrate()
    {
        $this->migrateClasses();
        $this->migrateArrays();
    }
}