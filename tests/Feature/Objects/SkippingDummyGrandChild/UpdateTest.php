<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\SkippingDummyGrandChild;

uses(SunhillDatabaseTestCase::class);

test('modify a dummy', function()
{
    SkippingDummyGrandChild::prepareDatabase($this);
    
    $test = new SkippingDummyGrandChild();
    $test->load(16);
    $test->dummyint = 20;
    $test->dummygrandchildint = 20;
    $test->commit();
    
    $this->assertDatabaseHas('dummies',['id'=>16,'dummyint'=>20]);
    $this->assertDatabaseHas('skippingdummychildren',['id'=>16]);
    $this->assertDatabaseHas('skippingdummygrandchildren',['id'=>16,'dummygrandchildint'=>20]);
});

