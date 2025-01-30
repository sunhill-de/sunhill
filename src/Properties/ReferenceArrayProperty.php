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
     
    public function getStructure(): \stdClass
    {
        $result = new \stdClass();
        $result->name = $this->getName();
        $result->type = $this->getAccessType();
        $result->element_type = $this->getAllowedElementTypes();
        
        return $result;
    }

    protected static function setupInfos()
    {
        static::addInfo('name', 'array');
        static::addInfo('description', 'A class for arrays.', true);
    }
    
}