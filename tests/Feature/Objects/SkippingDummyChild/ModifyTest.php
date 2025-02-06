<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\SkippingDummyChild;

uses(SunhillDatabaseTestCase::class);

test('modify a SkippingDummyChild', function()
{
    SkippingDummyChild::prepareDatabase($this);
    
    $test = new SkippingDummyChild();
    $test->load(14);
    $test->dummyint = 20;
    $test->commit();
    
    $this->assertDatabaseHas('dummies',['id'=>14,'dummyint'=>20]);
});

