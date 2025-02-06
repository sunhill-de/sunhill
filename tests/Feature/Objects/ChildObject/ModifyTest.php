<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\ChildObject;

uses(SunhillDatabaseTestCase::class);

test('modify a ChildObject', function()
{
    ChildObject::prepareDatabase($this);
    $test = new ChildObject();
    $test->load(9);
    
    $test->parent_int = 20;
    $test->parent_string = 'xyz';
    $test->parent_sarray[1] = 99;;
    $test->child_int = 123;
    $test->child_string = 'fed';
    $test->child_sarray = [9,8,7];
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>9,'parent_int'=>20,'parent_string'=>'xyz']);
    $this->assertDatabaseHas('childobjects',['id'=>9,'child_int'=>123,'child_string'=>'fed']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>1,'element'=>99]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>1,'element'=>8]);
});

