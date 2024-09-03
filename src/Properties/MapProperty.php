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

namespace Sunhill\Properties\Properties;

use Sunhill\Properties\Properties\Exceptions\InvalidParameterException;
use Mockery\Matcher\Type;
use Sunhill\Properties\Facades\Properties;
use Sunhill\Properties\Properties\Exceptions\InvalidValueException;

class MapProperty extends AbstractArrayProperty 
{

    protected $current = 0;
    
    protected $keys;
   
    public function current(): mixed
    {
        if (!$this->getStorage()->getIsInitialized($this->getName())) {
            $this->handleUninitialized();
        }        
        if (is_null($this->keys)) {
            $this->keys = $this->getStorage()->getKeys();
        }
        return $this->getStorage()->getIndexedValue($this->getName(), $this->keys[$this->current]);
    }
    
    
    public function key(): mixed
    {
        if (!$this->getStorage()->getIsInitialized($this->getName())) {
            $this->handleUninitialized();
        }
        return $this->keys[$this->current];        
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