<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\Scenarios\Attributes\AttributeScenario;
use Sunhill\Storage\MysqlStorages\MysqlAttributeStorage;
use Sunhill\Storage\Exceptions\AttributeNameNotSetException;

uses(SunhillDatabaseTestCase::class);

test('append a simple attribute via commit()', function()
{
    $scenario = new AttributeScenario($this);
    $scenario->migrate();
    $scenario->seed();
    
    $test = new MysqlAttributeStorage();
    $test->setValue('value',616);
    $test->setAttributeName('simpleintattribute');
    $test->commit();
    
    $this->assertDatabaseHas('attr_simpleintattribute',['id'=>6,'value'=>616]);
});

test('append a simple attribute via writeAttribute()', function()
{
    $scenario = new AttributeScenario($this);
    $scenario->migrate();
    $scenario->seed();
    
    $test = new MysqlAttributeStorage();
    $test->setValue('value',616);

    $test->writeAttribute('simpleintattribute');
    
    $this->assertDatabaseHas('attr_simpleintattribute',['id'=>6,'value'=>616]);
});

it('commit() fails when no attribute_name is set', function()
{
    $test = new MysqlAttributeStorage();
    $test->setValue('value',616);
    
    $test->commit();
})->throws(AttributeNameNotSetException::class);