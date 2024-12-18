<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\Scenarios\Attributes\AttributeScenario;
use Sunhill\Storage\MysqlStorages\MysqlAttributeMetaStorage;

uses(SunhillDatabaseTestCase::class);

test('read list of attributes', function()
{
    $scenario = new AttributeScenario($this);
    $scenario->migrate();
    $scenario->seed();
    
    $test = new MysqlAttributeMetaStorage();
    $test->load(1);
    
    expect($test->getValue('simpleintattribute'))->toBe(222);
    expect($test->getValue('simplestringattribute'))->toBe('jKl');    
});

test('update list of attributes (no change)', function()
{
    $scenario = new AttributeScenario($this);
    $scenario->migrate();
    $scenario->seed();
    
    $test = new MysqlAttributeMetaStorage();
    $test->setValue('simpleintattribute',929);
    $test->setValue('simplestringattribute','aDa');
    $test->forceID(10);
    $test->commit();
    
    $this->assertDatabaseHas('attributeobjectassigns',['container_id'=>10,'attribute_name'=>'simpleintattribute','attribute_id'=>6]);
    $this->assertDatabaseHas('attr_simpleintattribute',['id'=>6,'simpleintattribute'=>929]);
});

test('update list of attributes (values change)', function()
{
    $scenario = new AttributeScenario($this);
    $scenario->migrate();
    $scenario->seed();
    
});

test('update list of attributes (new attribute)', function()
{
    $scenario = new AttributeScenario($this);
    $scenario->migrate();
    $scenario->seed();
    
});

test('update list of attributes (removed attribute)', function()
{
    $scenario = new AttributeScenario($this);
    $scenario->migrate();
    $scenario->seed();
    
});

test('update list of attributes (cleared attributes)', function()
{
    $scenario = new AttributeScenario($this);
    $scenario->migrate();
    $scenario->seed();
    
});

test('delete list of attributes', function()
{
    $scenario = new AttributeScenario($this);
    $scenario->migrate();
    $scenario->seed();
    
});

