<?php
/**
 * @file MapArrayProperty.php
 * Defines an property for maps. Maps are array with a string as index
 * Lang de,en
 * Reviewstatus: 2024-02-04
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Properties/AbstractArrayPropertyTest.php 
 * Coverage: unknown
 */

namespace Sunhill\Properties;

use Sunhill\Properties\Exceptions\InvalidParameterException;
use Mockery\Matcher\Type;
use Sunhill\Facades\Properties;
use Sunhill\Properties\Exceptions\InvalidValueException;

class MapProperty extends ArrayProperty 
{

    public function __construct()
    {
        parent::__construct();
        $this->setIndexType('string');
    }
}