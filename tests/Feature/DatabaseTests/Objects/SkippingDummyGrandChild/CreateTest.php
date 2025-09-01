<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Illuminate\Support\Facades\DB;
use Sunhill\Tests\TestSupport\Objects\SkippingDummyGrandChild;

uses(SunhillDatabaseTestCase::class);

test('create a dummy', function()
{
    SkippingDummyGrandChild::prepareDatabase($this);
    $test = new SkippingDummyGrandChild();
    $test->create();
    $test->dummyint = 10;
    $test->dummygrandchildint = 12;
    $test->commit();
    
    $this->assertDatabaseHas('dummies',['id'=>$test->getID(),'dummyint'=>10]);
    $this->assertDatabaseHas('skippingdummychildren',['id'=>$test->getID()]);
    $this->assertDatabaseHas('skippingdummygrandchildren',['id'=>$test->getID(),'dummygrandchildint'=>12]);
});

