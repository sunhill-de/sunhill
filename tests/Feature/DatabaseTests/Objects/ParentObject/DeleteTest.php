<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\ParentObject;

uses(SunhillDatabaseTestCase::class);

test('delete a ParentObject', function()
{
    ParentObject::prepareDatabase($this);
    $write = new ParentObject();
    
    $write->delete(7);
    
    $this->assertDatabaseMissing('objects',['id'=>7]);
    $this->assertDatabaseMissing('parentobjects',['id'=>7]);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>7]);
});

