<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\ChildObject;

uses(SunhillDatabaseTestCase::class);

test('delete a ChildObject', function()
{
    ChildObject::prepareDatabase($this);
    $write = new ChildObject();
    
    $write->delete(9);
    
    $this->assertDatabaseMissing('objects',['id'=>9]);
    $this->assertDatabaseMissing('parentobjects',['id'=>9]);
    $this->assertDatabaseMissing('childobjects',['id'=>9]);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['id'=>9]);
    $this->assertDatabaseMissing('childobjects_child_sarray',['id'=>9]);
});

