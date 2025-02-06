<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\ParentReference;

uses(SunhillDatabaseTestCase::class);

test('modify a ParentReference', function()
{
    ParentReference::prepareDatabase($this);
    
    $test = new ParentReference();
    $test->load(17);
    $test->parent_int = 20;
    $test->commit();
    
    $this->assertDatabaseHas('parentreferences',['id'=>1,'parent_int'=>20]);
});

