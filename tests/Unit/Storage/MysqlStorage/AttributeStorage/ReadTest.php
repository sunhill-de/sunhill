<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\Scenarios\Attributes\AttributeScenario;
use Sunhill\Storage\MysqlStorages\MysqlAttributeStorage;

uses(SunhillDatabaseTestCase::class);

test('read a simple attribute by id', function()
{
    $scenario = new AttributeScenario($this);
    $scenario->migrate();
    $scenario->seed();
    
    $test = new MysqlAttributeStorage();
    $test->loadAttribute('simpleintattribute',3);
    expect($test->getValue('value'))->toBe(999);
});

test('read all attributes belonging to an object', function()
{
    $scenario = new AttributeScenario($this);
    $scenario->migrate();
    $scenario->seed();

    $test = new MysqlAttributeStorage();
    $test->loadForObject(1);
    expect($test->getValue('simpleintattribute'))->toBe(222);
    expect($test->getValue('simplestringattribute'))->toBe('jKl');    
});