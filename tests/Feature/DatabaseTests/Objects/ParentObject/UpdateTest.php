<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\ParentObject;

uses(SunhillDatabaseTestCase::class);

test('modify a ParentObject', function()
{
    ParentObject::prepareDatabase($this);
    $test = new ParentObject();
    $test->load(7);
    
    $test->parent_int = 20;
    $test->parent_string = 'def';
    $test->parent_sarray[] = 4;
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>7,'parent_int'=>20,'parent_String'=>'def']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>7,'index'=>3,'element'=>4]);
});
