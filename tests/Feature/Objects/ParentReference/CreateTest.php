<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Illuminate\Support\Facades\DB;
use Sunhill\Tests\TestSupport\Objects\ParentReference;

uses(SunhillDatabaseTestCase::class);

test('create a ParentReference', function()
{
    ParentReference::prepareDatabase($this);
    $test = new ParentReference();
    $test->create();
    $dummy1 = new Dummy();
    $dummy1->load(1);
    $dummy2 = new Dummy();
    $dummy2->load(2);
    $dummy3 = new Dummy();
    $dummy3->load(3);
    
    $test->parent_int = 10;
    $test->parent_reference = $dummy1;
    $test->parent_rarray = [$dummy2,$dummy3];
    $test->commit();
    
    $this->assertDatabaseHas('parentreferences',['id'=>$test->getID(),'parent_int'=>10,'parent_reference'=>1]);
    $this->assertDatabaseHas('parentreferences_parent_rarray',['container_id'=>$test->getID(),'index'=>0,'element'=>2]);
    $this->assertDatabaseHas('parentreferences_parent_rarray',['container_id'=>$test->getID(),'index'=>1,'element'=>3]);
});

