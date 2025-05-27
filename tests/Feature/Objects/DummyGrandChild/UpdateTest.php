<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\DummyChild;
use Sunhill\Tests\TestSupport\Objects\DummyGrandChild;

uses(SunhillDatabaseTestCase::class);

test('modify a dummygrandchild', function()
{
    DummyGrandChild::prepareDatabase($this);
    
    $test = new DummyGrandChild();
    $test->load(15);
    $test->dummyint = 20;
    $test->dummychildint = 99;
    $test->dummygrandchildint = 999;
    $test->commit();
    
    $this->assertDatabaseHas('dummies',['id'=>15,'dummyint'=>20]);
    $this->assertDatabaseHas('dummychildren',['id'=>15,'dummychildint'=>99]);
    $this->assertDatabaseHas('dummygrandchildren',['id'=>15,'dummygrandchildint'=>999]);
});

