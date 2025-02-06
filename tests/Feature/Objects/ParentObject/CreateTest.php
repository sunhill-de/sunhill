<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Illuminate\Support\Facades\DB;
use Sunhill\Tests\TestSupport\Objects\ParentObject;

uses(SunhillDatabaseTestCase::class);

test('create a ParentObject', function()
{
    ParentObject::prepareDatabase($this);
    $test = new ParentObject();
    $test->create();
    $test->parent_int = 10;
    $test->parent_string = 'abc';
    $test->parent_sarray = [1,2,3];
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>$test->getID(),'parent_int'=>10,'parent_string'=>'abc']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>$test->getID(),'index'=>1,'element'=>2]);
});

