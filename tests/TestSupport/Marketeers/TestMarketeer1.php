<?php

namespace Sunhill\Properties\Tests\TestSupport\Marketeers;

use Sunhill\Properties\InfoMarket\Marketeer;
use Sunhill\Properties\Storage\SimpleStorage;
use Sunhill\Properties\Tests\TestSupport\Storages\TestStorage1;

class TestMarketeer1 extends Marketeer
{
    
    protected function initializeElements()
    {
        $this->setName('marketeer1');
        
        $storage = new TestStorage1();
        $this->addElement('element1', $this->createProperty('string'))->setStorage($storage);
        $this->addElement('element2', $this->createProperty('string'))->setStorage($storage);
    }
}