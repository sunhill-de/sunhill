<?php

use Sunhill\Tests\SunhillKeepingDatabaseTestCase;
use Sunhill\Facades\Properties;
use Sunhill\Tests\TestSupport\Collections\ParentCollection;
use Illuminate\Support\Facades\DB;

uses(SunhillKeepingDatabaseTestCase::class);

test('migrate ParentCollection', function()
{
    Properties::registerProperty(ParentCollection::class);
    ParentCollection::migrate();
    $tables = DB::connection()->getSchemaBuilder()->getTables();
    $this->assertDatabaseHasTable('parentcollections');
    $this->assertDatabaseHasTable('parentcollections_parent_sarray');
});

test('create a ParentCollection', function()
{
    Properties::registerProperty(ParentCollection::class);
    ParentCollection::migrate();
    $test = new ParentCollection();
    $test->create();
    $test->parent_int = 10;
    $test->parent_string = 'abc';
    $test->parent_sarray = [1,2,3];
    $test->commit();
    
    $this->assertDatabaseHas('parentcollections',['id'=>1,'parent_int'=>10,'parent_string'=>'abc']);
    $this->assertDatabaseHas('parentcollections_parent_sarray',['container_id'=>1,'index'=>1,'element'=>2]);
})->depends('migrate ParentCollection');

test('load a ParentCollection', function()
{
    Properties::registerProperty(ParentCollection::class);
    ParentCollection::migrate();
    $write = new ParentCollection();
    $write->create();
    $write->parent_int = 10;
    $write->parent_string = 'abc';
    $write->parent_sarray = [1,2,3];
    $write->commit();
    
    $id = $write->getID();
    
    $test = new ParentCollection();
    $test->load($id);
    expect($test->parent_int)->toBe(10);
    expect($test->parent_string)->toBe('abc');
    expect($test->parent_sarray[1])->toBe(2);
})->depends('create a ParentCollection');

test('modify a ParentCollection', function()
{
    Properties::registerProperty(ParentCollection::class);
    ParentCollection::migrate();
    $write = new ParentCollection();
    $write->create();
    $write->parent_int = 10;
    $write->parent_string = 'abc';
    $write->parent_sarray = [1,2,3];
    $write->commit();
    
    $id = $write->getID();
    
    $test = new ParentCollection();
    $test->load($id);
    
    $test->parent_int = 20;
    $test->parent_string = 'def';
    $test->parent_sarray[] = 4;
    $test->commit();

    $this->assertDatabaseHas('parentcollections',['id'=>1,'parent_int'=>20,'parent_String'=>'def']);    
    $this->assertDatabaseHas('parentcollections_parent_sarray',['container_id'=>1,'index'=>3,'element'=>4]);
})->depends('load a ParentCollection');

test('delete a ParentCollection', function()
{
    Properties::registerProperty(ParentCollection::class);
    ParentCollection::migrate();
    $write = new ParentCollection();
    $write->create();
    $write->parent_int = 10;
    $write->parent_string = 'abc';
    $write->parent_sarray = [1,2,3];
    $write->commit();
    
    $write->delete(1);
    
    $this->assertDatabaseMissing('parentcollections',['id'=>1]);    
})->depends('load a ParentCollection');

