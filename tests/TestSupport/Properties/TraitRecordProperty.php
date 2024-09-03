<?php

namespace Sunhill\Properties\Tests\TestSupport\Properties;

use Sunhill\Properties\Properties\RecordProperty;

class TraitRecordProperty extends RecordProperty
{
    protected function initializeElements()
    {
        $this->addElement('ownelement1', new SimpleCharProperty());
        $record = new NonAbstractRecordProperty();
        $record->setName('child');
        $this->addElement('ownrecord', $record);
        $this->addTrait(new NonAbstractRecordProperty());
    }   
    
}

