<?php

namespace Sunhill\Tests\Feature\Properties;

use Sunhill\Types\TypeVarchar;
use Sunhill\Types\TypeInteger;
use Sunhill\Tests\Feature\Storages\SampleCallbackStorage;
use Sunhill\Properties\ArrayProperty;
use Sunhill\InfoMarket\Marketeer;

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