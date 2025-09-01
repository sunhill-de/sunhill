<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Illuminate\Support\Facades\DB;
use Sunhill\Tests\TestSupport\Objects\ArrayOnlyChildObject;

uses(SunhillDatabaseTestCase::class);

test('create a ArrayOnlyChildObject', function()
{
    ArrayOnlyChildObject::prepareDatabase($this);
    $test = new ArrayOnlyChildObject();
    $test->create();
    $test->parent_int = 10;
    $test->parent_string = 'ahg';
    $test->parent_sarray = [1,2,3];
    $test->child_sarray = [11,22,33];
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>$test->getID(),'parent_int'=>10,'parent_string'=>'ahg']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>$test->getID(),'element'=>2]);
    $this->assertDatabaseHas('arrayonlychildobjects',['id'=>$test->getID()]);
    $this->assertDatabaseHas('arrayonlychildobjects_child_sarray',['container_id'=>$test->getID(),'element'=>22]);
});

