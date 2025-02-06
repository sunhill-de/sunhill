<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\SkippingDummyGrandChild;

uses(SunhillDatabaseTestCase::class);

test('delete a dummy', function()
{
    SkippingDummyGrandChild::prepareDatabase($this);
    $write = new SkippingDummyGrandChild();
    
    $write->delete(16);
    
    $this->assertDatabaseMissing('dummies',['id'=>16]);
    $this->assertDatabaseMissing('skippingdummychildren',['id'=>16]);
    $this->assertDatabaseMissing('skippingdummygrandchildren',['id'=>16]);
});

