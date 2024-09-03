<?php

namespace Sunhill\Properties\Tests\Feature\Marketeers;

use Sunhill\Properties\InfoMarket\Marketeer;
use Sunhill\Properties\Types\TypeVarchar;
use Sunhill\Properties\Semantics\EMail;

class StaticMarketeer extends Marketeer
{
    
    protected function initializeElements()
    {
        $this->static()->setName('static');
        $this->addElement('string_element', new TypeVarchar())->setValue('ABCD');
        $this->addElement('email', new EMail())->setValue('test@example.com');
    }
   
}