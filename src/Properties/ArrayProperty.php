<?php
/**
 * @file AbstractArrayProperty.php
 * Defines an abstract property as base for all array like properties
 * Lang de,en
 * Reviewstatus: 2024-02-04
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/PropertyTest.php, Unit/PropertyValidateTest.php
 * Coverage: unknown
 */

namespace Sunhill\Properties\Properties;

use Sunhill\Properties\Properties\Exceptions\InvalidParameterException;
use Mockery\Matcher\Type;
use Sunhill\Properties\Facades\Properties;
use Sunhill\Properties\Properties\Exceptions\InvalidValueException;

class ArrayProperty extends AbstractArrayProperty 
{

    protected $current = 0;
    
    public function current(): mixed
    {
        if (!$this->getStorage()->getIsInitialized($this->getName())) {
            $this->handleUninitialized();
        }        
        return $this->getStorage()->getIndexedValue($this->getName(), $this->current);
    }
    
    
    public function key(): mixed
    {
        if (!$this->getStorage()->getIsInitialized($this->getName())) {
            $this->handleUninitialized();
        }
        return $this->current;        
    }
    
    public function next(): void
    {
        $this->current++;        
    }
    
    public function rewind(): void
    {
        $this->current = 0;        
    }
    
    public function valid(): bool
    {
        return (($this->current >= 0) && ($this->current < $this->count()));
    }
    
}