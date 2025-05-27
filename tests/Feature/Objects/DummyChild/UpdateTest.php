<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\DummyChild;

uses(SunhillDatabaseTestCase::class);

test('modify a dummychild', function()
{
    DummyChild::prepareDatabase($this);
    
    $test = new DummyChild();
    $test->load(13);
    $test->dummyint = 20;
    $test->dummychildint = 99;
    $test->commit();
    
    $this->assertDatabaseHas('dummies',['id'=>13,'dummyint'=>20]);
    $this->assertDatabaseHas('dummychildren',['id'=>13,'dummychildint'=>99]);
});

