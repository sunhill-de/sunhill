<?php

use Sunhill\Tests\SunhillKeepingDatabaseTestCase;
use Sunhill\Facades\Properties;
use Illuminate\Support\Facades\DB;
use Sunhill\Tests\TestSupport\Objects\ParentObject;
use Sunhill\Tests\TestSupport\Objects\ChildObject;

uses(SunhillKeepingDatabaseTestCase::class);

test('migrate ChildObject', function()
{
    Properties::registerProperty(ParentObject::class);
    Properties::registerProperty(ChildObject::class);
    ChildObject::migrate();
    
    $this->assertDatabaseHasTable('parentobjects');
    $this->assertDatabaseHasTable('parentobjects_parent_sarray');
    $this->assertDatabaseHasTable('childobjects');
    $this->assertDatabaseHasTable('childobjects_child_sarray');
});

test('create a ChildObject', function()
{
    $test = new ChildObject();
    $test->create();
    $test->parent_int = 10;
    $test->parent_string = 'abc';
    $test->parent_sarray = [1,2,3];
    $test->child_int = 111;
    $test->child_string = 'def';
    $test->child_sarray = [10,20,30];
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>1,'parent_int'=>10,'parent_string'=>'abc']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>1,'index'=>1,'element'=>2]);
    $this->assertDatabaseHas('childobjects',['id'=>1,'child_int'=>111,'child_string'=>'def']);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>1,'index'=>1,'element'=>20]);
})->depends('migrate ChildObject');

test('load a ChildObject', function()
{
    $write = new ChildObject();
    $write->create();
    $write->parent_int = 10;
    $write->parent_string = 'abc';
    $write->parent_sarray = [1,2,3];
    $write->child_int = 111;
    $write->child_string = 'def';
    $write->child_sarray = [10,20,30];
    $write->commit();
    
    $id = $write->getID();
    
    $test = new ChildObject();
    $test->load($id);
    expect($test->parent_int)->toBe(10);
    expect($test->parent_string)->toBe('abc');
    expect($test->parent_sarray[1])->toBe(2);
    expect($test->child_int)->toBe(111);
    expect($test->child_string)->toBe('def');
    expect($test->child_sarray[1])->toBe(20);
})->depends('create a ChildObject');

test('modify a ChildObject', function()
{
    $write = new ChildObject();
    $write->create();
    $write->parent_int = 10;
    $write->parent_string = 'abc';
    $write->parent_sarray = [1,2,3];
    $write->child_int = 111;
    $write->child_string = 'def';
    $write->child_sarray = [10,20,30];
    $write->commit();
    
    $id = $write->getID();
    
    $test = new ChildObject();
    $test->load($id);
    
    $test->parent_int = 20;
    $test->parent_string = 'xyz';
    $test->parent_sarray[1] = 99;;
    $test->child_int = 123;
    $test->child_string = 'fed';
    $test->child_sarray = [9,8,7];
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>1,'parent_int'=>20,'parent_string'=>'xyz']);    
    $this->assertDatabaseHas('childobjects',['id'=>1,'child_int'=>123,'child_string'=>'fed']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>1,'index'=>1,'element'=>99]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>1,'index'=>1,'element'=>8]);
})->depends('load a ChildObject');

test('delete a ParentObject', function()
{
    $write = new ChildObject();
    $write->create();
    $write->parent_int = 10;
    $write->parent_string = 'abc';
    $write->parent_sarray = [1,2,3];
    $write->child_int = 111;
    $write->child_string = 'def';
    $write->child_sarray = [10,20,30];
    $write->commit();
    
    $write->delete(1);
    
    $this->assertDatabaseMissing('parentobjects',['id'=>1]);    
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>1]);
    $this->assertDatabaseMissing('childobjects',['id'=>1]);
    $this->assertDatabaseMissing('childobjects_child_sarray',['container_id'=>1]);
})->depends('load a ChildObject');

