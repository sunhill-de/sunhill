<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\Scenarios\Attributes\AttributeScenario;
use Sunhill\Storage\MysqlStorages\MysqlAttributeStorage;
use Sunhill\Storage\Exceptions\IDNotFoundException;
use Sunhill\Storage\Exceptions\AttributeNameNotSetException;

uses(SunhillDatabaseTestCase::class);

test('read a simple attribute via loadAttribute()', function()
{
    $scenario = new AttributeScenario($this);
    $scenario->migrate();
    $scenario->seed();
    
    $test = new MysqlAttributeStorage();
    $test->loadAttribute('simpleintattribute',3);
    expect($test->getValue('simpleintattribute'))->toBe(999);
});

test('read a simple attribute via load()', function()
{
    $scenario = new AttributeScenario($this);
    $scenario->migrate();
    $scenario->seed();
    
    $test = new MysqlAttributeStorage();
    $test->setAttributeName('simpleintattribute');
    $test->load(3);
    expect($test->getValue('simpleintattribute'))->toBe(999);
});

it('fails when load() is called and no attribute is set',function()
{
    $test = new MysqlAttributeStorage();
    $test->load(3);    
})->throws(AttributeNameNotSetException::class);
