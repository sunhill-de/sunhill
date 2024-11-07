<?php
/**
 * @file ReferenceProperty.php
 * Defines a property that is a reference to a PooledRecordProperty
 * Lang en
 * Reviewstatus: 2024-11-07
 * Localization: complete
 * Documentation: complete
 * Tests: 
 * Coverage: 
 *
 * Wiki: 
 * tests 
 */

namespace Sunhill\Properties;

use Sunhill\Facades\Properties;
use Sunhill\Properties\Exceptions\NotAPropertyException;
use Sunhill\Properties\Exceptions\PropertyNameAlreadyGivenException;
use Sunhill\Properties\Exceptions\PropertyAlreadyInListException;
use Sunhill\Properties\Exceptions\PropertyHasNoNameException;
use Sunhill\Properties\Exceptions\InvalidInclusionException;
use Sunhill\Properties\Exceptions\NotAllowedInclusionException;
use Sunhill\Properties\Exceptions\PropertyNotFoundException;
use Sunhill\Storage\AbstractStorage;

class ReferenceProperty extends AbstractProperty 
{
   
}