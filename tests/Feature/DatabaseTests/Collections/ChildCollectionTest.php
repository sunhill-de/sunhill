<?php

use Sunhill\Tests\SunhillKeepingDatabaseTestCase;
use Sunhill\Facades\Properties;
use Sunhill\Tests\TestSupport\Collections\ParentCollection;
use Sunhill\Tests\TestSupport\Collections\ChildCollection;

uses(SunhillKeepingDatabaseTestCase::class);

test('migrate ChildCollection', function()
{
    Properties::registerProperty(ParentCollection::class);
    Properties::registerProperty(ChildCollection::class);
    ChildCollection::migrate();
    
    $this->assertDatabaseHasTable('childcollections');
    $this->assertDatabaseHasTable('childcollections_parent_sarray');
    $this->assertDatabaseHasTable('childcollections_child_sarray');
});

test('create a ChildCollection', function()
{
    Properties::registerProperty(ParentCollection::class);
    Properties::registerProperty(ChildCollection::class);
    ChildCollection::migrate();
    $test = new ChildCollection();
    $test->create();
    $test->parent_int = 10;
    $test->parent_string = 'abc';
    $test->parent_sarray = [1,2,3];
    $test->child_int = 111;
    $test->child_string = 'def';
    $test->child_sarray = [10,20,30];
    $test->commit();
    
    $this->assertDatabaseHas('childcollections',['id'=>1,'parent_int'=>10,'parent_string'=>'abc','child_int'=>111,'child_string'=>'def']);
    $this->assertDatabaseHas('childcollections_parent_sarray',['container_id'=>1,'index'=>1,'element'=>2]);
    $this->assertDatabaseHas('childcollections_child_sarray',['container_id'=>1,'index'=>1,'element'=>20]);
})->depends('migrate ChildCollection');

test('load a ChildCollection', function()
{
    Properties::registerProperty(ParentCollection::class);
    Properties::registerProperty(ChildCollection::class);
    ChildCollection::migrate();
    $write = new ChildCollection();
    $write->create();
    $write->parent_int = 10;
    $write->parent_string = 'abc';
    $write->parent_sarray = [1,2,3];
    $write->child_int = 111;
    $write->child_string = 'def';
    $write->child_sarray = [10,20,30];
    $write->commit();
    
    $id = $write->getID();
    
    $test = new ChildCollection();
    $test->load($id);
    expect($test->parent_int)->toBe(10);
    expect($test->parent_string)->toBe('abc');
    expect($test->parent_sarray[1])->toBe(2);
    expect($test->child_int)->toBe(111);
    expect($test->child_string)->toBe('def');
    expect($test->child_sarray[1])->toBe(20);
})->depends('create a ChildCollection');

test('modify a ChildCollection', function()
{
    Properties::registerProperty(ParentCollection::class);
    Properties::registerProperty(ChildCollection::class);
    ChildCollection::migrate();
    $write = new ChildCollection();
    $write->create();
    $write->parent_int = 10;
    $write->parent_string = 'abc';
    $write->parent_sarray = [1,2,3];
    $write->child_int = 111;
    $write->child_string = 'def';
    $write->child_sarray = [10,20,30];
    $write->commit();
    
    $id = $write->getID();
    
    $test = new ChildCollection();
    $test->load($id);
    
    $test->parent_int = 20;
    $test->parent_string = 'xyz';
    $test->parent_sarray[1] = 99;;
    $test->child_int = 123;
    $test->child_string = 'fed';
    $test->child_sarray = [9,8,7];
    $test->commit();
    
    $this->assertDatabaseHas('childcollections',['id'=>1,'parent_int'=>20,'parent_string'=>'xyz','child_int'=>123,'child_string'=>'fed']);
    $this->assertDatabaseHas('childcollections_parent_sarray',['container_id'=>1,'index'=>1,'element'=>99]);
    $this->assertDatabaseHas('childcollections_child_sarray',['container_id'=>1,'index'=>1,'element'=>8]);
})->depends('load a ChildCollection');

test('delete a ParentCollection', function()
{
    Properties::registerProperty(ParentCollection::class);
    Properties::registerProperty(ChildCollection::class);
    ChildCollection::migrate();
    $write = new ChildCollection();
    $write->create();
    $write->parent_int = 10;
    $write->parent_string = 'abc';
    $write->parent_sarray = [1,2,3];
    $write->child_int = 111;
    $write->child_string = 'def';
    $write->child_sarray = [10,20,30];
    $write->commit();
    
    $write->delete(1);
    
    $this->assertDatabaseMissing('childcollections_parent_sarray',['container_id'=>1]);
    $this->assertDatabaseMissing('childcollections',['id'=>1]);
    $this->assertDatabaseMissing('childcollections_child_sarray',['container_id'=>1]);
})->depends('load a ChildCollection');

