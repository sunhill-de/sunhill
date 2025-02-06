<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\SkippingDummyChild;

uses(SunhillDatabaseTestCase::class);

test('delete a dummy', function()
{
    SkippingDummyChild::prepareDatabase($this);
    $write = new SkippingDummyChild();
    
    $write->delete(14);
    
    $this->assertDatabaseMissing('dummies',['id'=>14]);
    $this->assertDatabaseMissing('skippingdummychildren',['id'=>14]);
});

