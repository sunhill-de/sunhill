<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\ArrayOnlyChildObject;

uses(SunhillDatabaseTestCase::class);

test('delete an ArrayOnlyChildObject', function()
{
    ArrayOnlyChildObject::prepareDatabase($this);
    $write = new ArrayOnlyChildObject();
    
    $write->delete(20);
    
    $this->assertDatabaseMissing('parentobjects',['id'=>20]);
    $this->assertDatabaseMissing('arrayonlychildobjects',['id'=>20]);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>20]);
    $this->assertDatabaseMissing('arrayonlychildobjects_child_sarray',['container_id'=>20]);
    
});

