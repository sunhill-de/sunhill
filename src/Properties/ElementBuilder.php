<?php
/**
 * @file ElementBuilder.php
 * A mediator class for adding elements to a record property
 * Lang en
 * Reviewstatus: 2024-09-29
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/PropertyTest.php, Unit/PropertyValidateTest.php
 * Coverage: unknown
 */

namespace Sunhill\Properties;

class ElementBuilder
{
    
    protected $owner;
    
    public function __construct(RecordProperty $owner)
    {
        $this->onwer = $owner;    
    }
}