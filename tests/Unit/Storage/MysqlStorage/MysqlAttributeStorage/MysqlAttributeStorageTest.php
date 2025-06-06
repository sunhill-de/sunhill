<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Storage\MysqlStorage\MysqlAttributeStorage;
use Illuminate\Database\RecordNotFoundException;

uses(SunhillDatabaseTestCase::class);

test('loadAttribute() pass', function()
{
    Dummy::prepareDatabase($this);
    $test = new MysqlAttributeStorage();
    
    expect($test->loadAttribute(1,1)->name)->toBe('str_attribute');    
    expect($test->loadAttribute(1,1)->type)->toBe('string');
    expect($test->loadAttribute(1,1)->value)->toBe('attribute');
});

test('loadAttribute() fail (no container)', function()
{
    Dummy::prepareDatabase($this);
    $test = new MysqlAttributeStorage();
    
    $test->loadAttribute(1000,1)->name;
})->throws(RecordNotFoundException::class);

test('loadAttribute() fail (no attribute)', function()
{
    Dummy::prepareDatabase($this);
    $test = new MysqlAttributeStorage();
    
    $test->loadAttribute(1,1000)->name;
})->throws(RecordNotFoundException::class);

test('searchAttribute() pass', function()
{
    Dummy::prepareDatabase($this);
    $test = new MysqlAttributeStorage();
    
    expect($test->searchAttribute('str_attribute')->id)->toBe(1);
});

test('searchAttribute() fail', function()
{
    Dummy::prepareDatabase($this);
    $test = new MysqlAttributeStorage();
    
    expect($test->searchAttribute('unknown'))->toBe(null);
});

test('storeAttribute works with new attribute', function()
{
   Dummy::prepareDatabase($this);
   $test = new MysqlAttributeStorage();
   
   $test->storeAttribute(4,2,'bce');
   $this->assertDatabaseHas('attr_another_string',['container_id'=>2,'value'=>'bce']);
});

test('storeAttribute works with stored attribute', function()
{
    Dummy::prepareDatabase($this);
    $test = new MysqlAttributeStorage();
    
    $test->storeAttribute(1,1,'bce');
    $this->assertDatabaseHas('attr_str_attribute',['container_id'=>1,'value'=>'bce']);
});

test('storeAttribute works with unknown attribute', function()
{
    Dummy::prepareDatabase($this);
    $test = new MysqlAttributeStorage();
    
    $test->storeAttribute(999,1,'bce');
})->throws(RecordNotFoundException::class);