<?php

namespace Sunhill\Properties\Tests\TestSupport\Properties;

use Sunhill\Properties\Properties\RecordProperty;

class NonAbstractRecordProperty extends RecordProperty
{
    public function isValid($test): bool
    {
        return false;
    }
  
    protected function initializeElements()
    {
        $element1 = new SimpleCharProperty();
        $element1->setName('elementA');
        $element1->setOwner($this);
        
        $element2 = new SimpleCharProperty();
        $element2->setName('elementB');
        $element2->setOwner($this);
        
        $this->elements['elementA'] = $element1;
        $this->elements['elementB'] = $element2;
    }
    
    
}

