<?php

namespace Sunhill\Properties\Tests\Feature\Properties;

use Sunhill\Properties\Types\TypeVarchar;
use Sunhill\Properties\Types\TypeInteger;
use Sunhill\Properties\Tests\Feature\Storages\SampleCallbackStorage;
use Sunhill\Properties\Properties\ArrayProperty;
use Sunhill\Properties\InfoMarket\Marketeer;

class SampleCallbackProperty extends Marketeer
{
    
    protected function initializeElements()
    {
        $this->setName('callback');
        $this->addElement('sample_string', new TypeVarchar());
        $this->addElement('sample_integer', new TypeInteger());
        $this->addElement('sample_array', new ArrayProperty())->setAllowedElementTypes(TypeVarchar::class);
        $this->setStorage(new SampleCallbackStorage());
    }
}