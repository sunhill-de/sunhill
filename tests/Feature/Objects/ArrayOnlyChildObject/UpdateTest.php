<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\ArrayOnlyChildObject;

uses(SunhillDatabaseTestCase::class);

test('modify a ArrayOnlyChildObject', function()
{
    ArrayOnlyChildObject::prepareDatabase($this);
    
    $test = new ArrayOnlyChildObject();
    $test->load(20);
    $test->parent_int = 20;
    $test->parent_string = 'asa';
    $test->parent_sarray[] = 919;
    $test->child_sarray[] = 191;
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>20,'parent_int'=>20,'parent_string'=>'asa']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>20,'element'=>919,'index'=>3]);
    $this->assertDatabaseHas('arrayonlychildobjects',['id'=>20]);
    $this->assertDatabaseHas('arrayonlychildobjects_child_sarray',['container_id'=>20,'element'=>191]);
    
});

