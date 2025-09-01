<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Illuminate\Support\Facades\DB;
use Sunhill\Tests\TestSupport\Objects\ChildObject;

uses(SunhillDatabaseTestCase::class);

test('create a ChildObject', function()
{
    ChildObject::prepareDatabase($this);
    $test = new ChildObject();
    $test->create();
    $test->parent_int = 10;
    $test->parent_string = 'abc';
    $test->parent_sarray = [1,2,3];
    $test->child_int = 111;
    $test->child_string = 'def';
    $test->child_sarray = [10,20,30];
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>$test->getID(),'parent_int'=>10,'parent_string'=>'abc']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>$test->getID(),'index'=>1,'element'=>2]);
    $this->assertDatabaseHas('childobjects',['id'=>$test->getID(),'child_int'=>111,'child_string'=>'def']);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>$test->getID(),'index'=>1,'element'=>20]);
});
