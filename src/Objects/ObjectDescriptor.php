<?php

namespace Sunhill\Properties\Objects;

use Sunhill\Properties\Facades\Properties;
use Sunhill\Properties\Objects\Exceptions\NameNotGivenException;
use Sunhill\Properties\Properties\AbstractProperty;

class ObjectDescriptor
{
    
    protected $owner;
    
    protected $source = '';
    
    public function __construct($owner)
    {
        $this->owner = $owner;    
    }
    
    public function setSourceStorage(string $source)
    {
        $this->source = $source;    
    }
    
    public function __call(string $name,$params)
    {
        if (!isset($params[0])) {
            throw new NameNotGivenException("The property '$name' has no name");
        }
        return $this->owner->appendElement($params[0], $name, $this->source);
    }
    
    public function embed(string $property): AbstractProperty
    {
        return $this->owner->embedElement($property);
    }
    
    public function include(string $property): AbstractProperty
    {
        return $this->owner->includeElement($property, $this->source);        
    }
    
}