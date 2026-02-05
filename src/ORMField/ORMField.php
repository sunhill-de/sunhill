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
 * @author klaus
 *
 */
class ORMField
{
    
    /**
     * The name of this field
     * @var string
     */
    protected string $name = '';
    
    /**
     * The type of this field
     * @var string
     */
    protected string $type = '';
    
    /**
     * Constructor for a field. Takes the name and the type of this field
     * 
     * @param string $name
     * @param string $type
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
     */
    public function setName(string $name): static
    {
        
    }
    
    /**
     * getter for the name field
     * 
     * @return string
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
     */
    public function setType(string $type): static
    {
        
    }
    
    /**
     * getter for the type field
     * 
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
    
}