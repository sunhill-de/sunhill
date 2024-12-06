<?php

/**
 * @file TypeCalculated.php
 * Defines a type for calculated
 * Lang en
 * Reviewstatus: 2024-02-05
 * Localization: complete
 * Documentation: complete
 * Tests: 
 * Coverage: unknown
 */

namespace Sunhill\Types;

use Sunhill\Properties\AbstractSimpleProperty;
use Sunhill\Properties\Exceptions\NoCallbackSetException;

class TypeCalculated extends AbstractSimpleProperty
{
    
    protected $callback;
    
    /**
     * Tests if input can be solved to an boolean (always false, because calculated are read only
     * 
     * {@inheritDoc}
     * @see Sunhill\\ValidatorBase::isValid()
     */
    public function isValid($input): bool
    {
        return false;
    }
    
    public function isInitialized(): bool
    {
        return true;
    }
    
    public function isWriteable(): bool
    {
        return false;
    }
    
    public function isReadable(): bool
    {
        return true;
    }
    
    public static function getAccessType(): string
    {
        return 'string';
    }
    
    public function setCallback(callable $callback): self
    {
        $this->callback = $callback;
        return $this;
    }
    
    public function getValue()
    {
        if (!is_callable($this->callback)) {
            throw new NoCallbackSetException("In '".$this->getName()."' is no callback defined.");
        }
        $callback = $this->callback;
        $value = $callback($this);
        
        $this->getStorage()->setValue($this->getName(), $value);
        return $value;
    }
    
    /**
     * This method must be overwritten by the derrived class to define its infos
     * Test: /Unit/Objects/PropertyCollection_infoTest
     */
    protected static function setupInfos()
    {
        static::addInfo('name', 'calculated');
        static::addInfo('description', 'The basic type for caclulated fields.', true);
        static::addInfo('type', 'basic');
    }
    
}
