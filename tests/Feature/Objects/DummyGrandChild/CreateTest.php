<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Illuminate\Support\Facades\DB;
use Sunhill\Tests\TestSupport\Objects\DummyChild;
use Sunhill\Tests\TestSupport\Objects\DummyGrandChild;

uses(SunhillDatabaseTestCase::class);

test('create a DummyGrandChild', function()
{
    DummyGrandChild::prepareDatabase($this);
    $test = new DummyGrandChild();
    $test->create();
    $test->dummyint = 10;
    $test->dummychildint = 11;
    $test->dummygrandchildint = 12;
    $test->commit();
    
    $this->assertDatabaseHas('dummies',['id'=>$test->getID(),'dummyint'=>10]);
    $this->assertDatabaseHas('dummychildren',['id'=>$test->getID(),'dummychildint'=>11]);
    $this->assertDatabaseHas('dummygrandchildren',['id'=>$test->getID(),'dummygrandchildint'=>12]);
});

