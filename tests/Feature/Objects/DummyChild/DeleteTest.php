<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\DummyChild;

uses(SunhillDatabaseTestCase::class);

test('delete a dummychild', function()
{
    DummyChild::prepareDatabase($this);
    $write = new DummyChild();
    
    $write->delete(1);
    
    $this->assertDatabaseMissing('dummies',['id'=>1]);
    $this->assertDatabaseMissing('dummychildren',['id'=>1]);
});

