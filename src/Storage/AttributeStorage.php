<?php
/**
 * @file AttributeStorage.php
 * A basic class for attrbute storages. As a descendant of PersistentPoolStorage it inherites all functions
 * about id handling, etc.
 *
 * @author Klaus Dimde
 * Lang en
 * Reviewstatus: 2024-12-15
 * Localization: none
 * Documentation: 
 * Tests: 
 * Coverage: 
 */

namespace Sunhill\Storage;

/**
 * @author klaus
 *
 */
abstract class AttributeStorage extends PersistentPoolStorage
{
    
    /**
     * Calculates the storage id (e.g. table name) for the given storage by prefixing 'attr_'
     * 
     * @param string $attribute_name
     * @return string
     */
    protected function calculateAttributeStorageID(string $attribute_name): string
    {
        return 'attr_'.$attribute_name;
    }
    
    /**
     * Loads the attributes belonging to an object identified by $id
     * 
     * @param int $id
     */
    abstract protected function doLoadForObject(int $id);
    
    /**
     * Loads the attribute names $attribute_name with the id $attribute_id
     * 
     * @param string $attribute_name
     * @param int $attribute_id
     */
    abstract protected function doLoadAttribute(string $attribute_name, int $attribute_id);
    
    /**
     * Loads the attribute names $attribute_name with the id $attribute_id
     * (just calls doLoadAttribute())
     *  
     * @param string $sttribute_name
     * @param int $attribute_id
     */
    public function loadAttribute(string $sttribute_name,int $attribute_id)
    {
        $this->doLoadAttribute($sttribute_name, $attribute_id);
    }
    
    /**
     * Loads all attributes for the given object $id
     * 
     * @param int $id
     */
    public function loadForObject(int $id)
    {
        $this->doLoadForObject($id);
    }
}