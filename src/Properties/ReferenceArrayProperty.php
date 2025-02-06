<?php
/**
 * @file ReferenceArrayProperty.php
 * Defines an array of references to PooledRecordProperties
 * Lang de,en
 * Reviewstatus: 2024-11-07
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/PropertyTest.php, Unit/PropertyValidateTest.php
 * Coverage: 02% (2024-11-13)
 * 
 * Wiki: /Array_properties
 */

namespace Sunhill\Properties;

use Sunhill\Properties\Exceptions\InvalidParameterException;
use Sunhill\Facades\Properties;
use Sunhill\Properties\Exceptions\InvalidValueException;
use Sunhill\Properties\Exceptions\InvalidIndexTypeException;
use Sunhill\Properties\Exceptions\InvalidIndexException;

class ReferenceArrayProperty extends ArrayProperty
{
     
    /**
     * @todo At the momemnt we assuma a reference to by an integer. It could be a string too
     * {@inheritDoc}
     * @see \Sunhill\Properties\ArrayProperty::getStructure()
     */
    public function getStructure(): \stdClass
    {
        $result = new \stdClass();
        $result->name = $this->getName();
        $result->type = 'array';
        $result->element_type = 'integer';
        $result->index_type = 'integer';
        
        return $result;
    }

    protected static function setupInfos()
    {
        static::addInfo('name', 'array');
        static::addInfo('description', 'A class for arrays.', true);
    }
    
}