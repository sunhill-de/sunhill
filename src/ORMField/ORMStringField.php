<?php
/**
 * @file ORMField/ORMStringField.php
 * Provides the ORMStringField class.
 * Last reviewed: 2026-02-06
 * Created: 2026-02-05
 * Author: Klaus
 */

namespace Sunhill\ORMField;

/**
 * Class ORMStringField
 * 
 * Defines a string field
 * Besides all the options a field can have (@see ORMField) a string field can define:
 * - max_length The maximum length of a string
 * - max_length_exceed_policy (cut|exception) The policy how a string should be handled that is longer that max_lengzh
 * 
 * @package ORM
 * @author klaus
 * @since 1.0.0
 *
 */
class ORMStringField extends ORMField
{
    
    /**
     * constructor for a string field. Sets the type of the field to string
     * 
     * @param string $name
     */
    public function __construct(string $name)
    {
        parent::__construct($name, 'string');
    }
}