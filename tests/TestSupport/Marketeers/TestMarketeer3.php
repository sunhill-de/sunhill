<?php

namespace Sunhill\Tests\TestSupport\Marketeers;

use Sunhill\InfoMarket\Marketeer;
use Sunhill\Storage\SimpleStorage;
use Sunhill\Storage\SimpleWriteableStorage;
use Sunhill\Tests\TestSupport\Storages\TestStorage3;

class TestMarketeer3 extends Marketeer
{
    
    protected function initializeElements()
    {
        $storage = new TestStorage3();
        $this->addElement('stringkey', $this->createProperty('string'))->setStorage($storage);
        $this->addElement('floatkey', $this->createProperty('float'))->setStorage($storage);
        $this->addElement('intkey', $this->createProperty('integer'))->setStorage($storage);
        $this->addElement('boolkey', $this->createProperty('boolean'))->setStorage($storage);
        $this->addElement('textkey', $this->createProperty('text'))->setStorage($storage);
        $this->addElement('datekey', $this->createProperty('date'))->setStorage($storage);
        $this->addElement('timekey', $this->createProperty('time'))->setStorage($storage);
        $this->addElement('datetimekey', $this->createProperty('datetime'))->setStorage($storage);
    }
}