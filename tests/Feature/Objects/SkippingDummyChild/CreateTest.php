<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Illuminate\Support\Facades\DB;
use Sunhill\Tests\TestSupport\Objects\SkippingDummyChild;

uses(SunhillDatabaseTestCase::class);

test('create a SkippingDummyChild', function()
{
    SkippingDummyChild::prepareDatabase($this);
    $test = new SkippingDummyChild();
    $test->create();
    $test->dummyint = 10;
    $test->commit();
    
    $this->assertDatabaseHas('dummies',['id'=>$test->getID(),'dummyint'=>10]);
    $this->assertDatabaseHas('skippingdummychildren',['id'=>$test->getID()]);
});

