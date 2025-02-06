<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;

uses(SunhillDatabaseTestCase::class);

test('delete a dummy', function()
{
    Dummy::prepareDatabase($this);
    $write = new Dummy();
    
    $write->delete(1);
    
    $this->assertDatabaseMissing('dummies',['id'=>1,'dummyint'=>20]);
});

