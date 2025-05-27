<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;

uses(SunhillDatabaseTestCase::class);

test('modify a dummy', function()
{
    Dummy::prepareDatabase($this);
    
    $test = new Dummy();
    $test->load(1);
    $test->dummyint = 20;
    $test->commit();
    
    $this->assertDatabaseHas('dummies',['id'=>1,'dummyint'=>20]);
});

