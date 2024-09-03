<?php

namespace Sunhill\Properties\Tests\TestSupport\Marketeers;

use Sunhill\Properties\InfoMarket\Marketeer;
use Sunhill\Properties\Storage\SimpleStorage;
use Sunhill\Properties\Tests\TestSupport\Storages\TestStorage1;

class TestMarketeer2 extends Marketeer
{
    
    protected function initializeElements()
    {
        $storage = new TestStorage1();
        $this->addElement('key1', $this->createProperty('string'))->setStorage($storage);
        $this->addElement('key2', $this->createProperty('string'))->setStorage($storage);
        $this->addElement('key3', new TestMarketeer1());
    }
}