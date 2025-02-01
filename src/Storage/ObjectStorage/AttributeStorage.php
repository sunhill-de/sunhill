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

namespace Sunhill\Storage\ObjectStorage;

use Sunhill\Query\BasicQuery;
use Sunhill\Storage\Exceptions\AttributeNameNotSetException;
use Sunhill\Storage\PersistentPoolStorage;

/**
 * @author klaus
 *
 */
abstract class AttributeStorage extends PersistentPoolStorage
{

    /**
     * The storage id of the storage
     * 
     * @var string
     */
    protected string $attribute_name = '';
    
    /**
     * Setter for attribute_name
     * 
     * @param string $attribute_name
     * @return self
     */
    public function setAttributeName(string $attribute_name): self
    {
        $this->attribute_name = $attribute_name;
        return $this;
    }
    
    /**
     * Getter for attribute_name
     * 
     * @return string
     */
    public function getAttributeName(): string
    {
        return $this->attribute_name;    
    }
    
    /**
     * Overwrite of the inhertied method to check if a attribute_name is set
     * 
     * {@inheritDoc}
     * @see \Sunhill\Storage\PersistentPoolStorage::load()
     */
    public function load($id)
    {
        if (empty($this->attribute_name)) {
            throw new AttributeNameNotSetException("There is no attribute name set in storage");
        }
        return parent::load($id);
    }
    
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
     * Loads the attribute names $attribute_name with the id $attribute_id
     * (just calls doLoadAttribute())
     *  
     * @param string $sttribute_name
     * @param int $attribute_id
     */
    public function loadAttribute(string $attribute_name,int $attribute_id)
    {
        $this->setAttributeName($attribute_name);
        return $this->load($attribute_id);
    }
        
}