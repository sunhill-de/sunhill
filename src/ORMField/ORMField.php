<?php
/**
 * @file ORMField/ORMField.php
 * Provides the ORMField class.
 * Last reviewed: 2026-02-05
 * Created: 2026-02-05
 * Author: Klaus
 */

namespace Sunhill\ORMField;

/**
 * Class ORMField
 * 
 * The basic class of a field. It stores at leat the following information:
 *  - name of the field
 *  - the type of the field
 * Optional are the following informations:
 *  - default value
 *  - if this field is readable
 *  - if this field is writeable
 *  - neccessary read capabilities
 *  - neccessary write capabilities
 *   
 * @package ORM
 * @author klaus
 * @since 1.0.0
 *
 */
class ORMField
{
    
    /**
     * The name of this field
     * @var string
     * @since 1.0.0
     */
    protected string $name = '';
    
    /**
     * The type of this field
     * @var string
     * @since 1.0.0
     */
    protected string $type = '';
    
    /**
     * Stores additional attributes of this field
     * @var array
     */
    protected array $attributes = [];
    
    /**
     * Constructor for a field. Takes the name and the type of this field
     * 
     * @param string $name
     * @param string $type
     * @since 1.0.0
     */
    public function __construct(string $name, string $type)
    {
        $this->setName($name);
        $this->setType($type);
    }
    
    /**
     * setter for the name field
     * 
     * @param string $name
     * @return static
     * @throws ORMFieldException
     * @since 1.0.0
     */
    public function setName(string $name): static
    {
        if (empty($name)) {
            throw new ORMFieldException('"name" must not be empty');
        }
        $this->name = $name;
        
        return $this;
    }
    
    /**
     * getter for the name field
     * 
     * @return string
     * @since 1.0.0
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * setter for the type field
     * 
     * @param string $type
     * @return static
     * @throws ORMFieldException
     * @since 1.0.0
     */
    public function setType(string $type): static
    {
        if (empty($type)) {
            throw new ORMFieldException('"type" must not be empty');
        }
        $this->type = $type;
        
        return $this;        
    }
    
    /**
     * getter for the type field
     * 
     * @return string
     * @since 1.0.0
     */
    public function getType(): string
    {
        return $this->type;
    }
    
    /**
     * Test if an attribute exists
     * 
     * @param string $attribute
     * @return bool true if it exists otherwise false
     * @since 1.0.0
     */
    public function hasAttribute(string $attribute): bool
    {
        return array_key_exists($attribute, $this->attributes); 
    }
    
    
    /**
     * Sets the value of an attribute
     * 
     * @param string $attribute
     * @param unknown $value
     * @return static $this
     * @since 1.0.0
     */
    public function setAttribute(string $attribute, $value): static
    {
        $this->attributes[$attribute] = $value;
        
        return $this;
    }
    
    /**
     * Returns the value of attribute $attribute or throws an exception when this attribute was 
     * 
     * @param string $attribute
     * @return The value of the attribute
     * @throws ORMFieldException if the attribute does not exist
     * @since 1.0.0
     */
    public function getAttribute(string $attribute)
    {
        if (!$this->hasAttribute($attribute)) {
            throw new ORMFieldException("The attribute '$attribute' does not exist.");
        }
        
        return $this->attributes[$attribute];
    }
    
    /**
     * Removes the attribute from the list
     * 
     * @param string $attribute
     * @return static
     * @since 1.0.0
     */
    public function unsetAttribute(string $attribute): static
    {
        unset($this->attributes[$attribute]);
        
        return $this;
    }
}