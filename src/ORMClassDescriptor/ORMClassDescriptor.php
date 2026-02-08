<?php
/**
 * @file ORMClassDescriptor/ORMClassDescriptor.php
 * Provides the ORMClassDescriptor class.
 * Last reviewed: 2026-02-05
 * Created: 2026-02-05
 * Author: Klaus
 */

namespace Sunhill\ORMClassDescriptor;

use Sunhill\ORMField\ORMStringField;
use Sunhill\ORMField\ORMField;

/**
 * Class ORMClassDescriptor
 * 
 * This class describes a sunhill orm class. That is in detalil:
 * - description of each field with at least type and name
 * - description of the class like
 *   - class internal name
 *   - class description
 *   - storage name
 *
 * @package ORM
 * @author klaus
 * @since 1.0.0
 */
class ORMClassDescriptor
{
    
    protected $fields = [];
    
    /**
     * Adds a string field to the descriptor with the name $name and returns the field desciptor
     * 
     * @param string $name
     * @return ORMStringField
     * @since 1.0.0
     */
    public function string(string $name): ORMStringField
    {
        $field = new ORMStringField($name);
        
        $this->addField($field);
        
        return $field;
    }
    
    protected function addField(ORMField $field)
    {
        
    }
}