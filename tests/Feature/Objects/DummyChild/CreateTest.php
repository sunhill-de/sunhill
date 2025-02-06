<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Illuminate\Support\Facades\DB;
use Sunhill\Tests\TestSupport\Objects\DummyChild;

uses(SunhillDatabaseTestCase::class);

test('create a dummychild', function()
{
    DummyChild::prepareDatabase($this);
    $test = new DummyChild();
    $test->create();
    $test->dummyint = 10;
    $test->dummychildint = 11;
    $test->commit();
    
    $this->assertDatabaseHas('dummies',['id'=>$test->getID(),'dummyint'=>10]);
    $this->assertDatabaseHas('dummychildren',['id'=>$test->getID(),'dummychildint'=>11]);
});

