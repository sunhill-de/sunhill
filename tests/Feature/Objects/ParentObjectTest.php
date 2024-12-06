<?php

use Sunhill\Tests\SunhillKeepingDatabaseTestCase;
use Sunhill\Facades\Properties;
use Illuminate\Support\Facades\DB;
use Sunhill\Tests\TestSupport\Objects\ParentObject;

uses(SunhillKeepingDatabaseTestCase::class);

test('migrate ParentObject', function()
{
    Properties::registerProperty(ParentObject::class);
    ParentObject::migrate();
    
    $this->assertDatabaseHasTable('parentobjects');
    $this->assertDatabaseHasTable('parentobjects_parent_sarray');
});

test('create a ParentObject', function()
{
    $test = new ParentObject();
    $test->create();
    $test->parent_int = 10;
    $test->parent_string = 'abc';
    $test->parent_sarray = [1,2,3];
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>1,'parent_int'=>10,'parent_string'=>'abc']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>1,'index'=>1,'element'=>2]);
})->depends('migrate ParentObject');

test('load a ParentObject', function()
{
    $write = new ParentObject();
    $write->create();
    $write->parent_int = 10;
    $write->parent_string = 'abc';
    $write->parent_sarray = [1,2,3];
    $write->commit();
    
    $id = $write->getID();
    
    $test = new ParentObject();
    $test->load($id);
    expect($test->parent_int)->toBe(10);
    expect($test->parent_string)->toBe('abc');
    expect($test->parent_sarray[1])->toBe(2);
})->depends('create a ParentObject');

test('modify a ParentObject', function()
{
    $write = new ParentObject();
    $write->create();
    $write->parent_int = 10;
    $write->parent_string = 'abc';
    $write->parent_sarray = [1,2,3];
    $write->commit();
    
    $id = $write->getID();
    
    $test = new ParentObject();
    $test->load($id);
    
    $test->parent_int = 20;
    $test->parent_string = 'def';
    $test->parent_sarray[] = 4;
    $test->commit();

    $this->assertDatabaseHas('parentobjects',['id'=>1,'parent_int'=>20,'parent_String'=>'def']);    
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>1,'index'=>3,'element'=>4]);
})->depends('load a ParentObject');

test('delete a ParentObject', function()
{
    $write = new ParentObject();
    $write->create();
    $write->parent_int = 10;
    $write->parent_string = 'abc';
    $write->parent_sarray = [1,2,3];
    $write->commit();
    
    $write->delete(1);
    
    $this->assertDatabaseMissing('parentobjects',['id'=>1]);    
})->depends('load a ParentObject');

