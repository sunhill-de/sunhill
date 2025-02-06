<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\DummyChild;
use Sunhill\Tests\TestSupport\Objects\DummyGrandChild;

uses(SunhillDatabaseTestCase::class);

test('delete a DummyGrandChild', function()
{
    DummyGrandChild::prepareDatabase($this);
    $write = new DummyGrandChild();
    
    $write->delete(1);
    
    $this->assertDatabaseMissing('dummies',['id'=>1]);
    $this->assertDatabaseMissing('dummychildren',['id'=>1]);
    $this->assertDatabaseMissing('dummygrandchildren',['id'=>1]);
});

