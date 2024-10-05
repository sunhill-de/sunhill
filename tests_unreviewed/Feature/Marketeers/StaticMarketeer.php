<?php

namespace Sunhill\Tests\Feature\Marketeers;

use Sunhill\InfoMarket\Marketeer;
use Sunhill\Types\TypeVarchar;
use Sunhill\Semantics\EMail;

class StaticMarketeer extends Marketeer
{
    
    protected function initializeElements()
    {
        $this->static()->setName('static');
        $this->addElement('string_element', new TypeVarchar())->setValue('ABCD');
        $this->addElement('email', new EMail())->setValue('test@example.com');
    }
   
}