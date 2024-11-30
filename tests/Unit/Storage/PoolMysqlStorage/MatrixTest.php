<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\Scenarios\Obejcts\DummyScenario;
use Sunhill\Storage\PoolMysqlStorage\PoolMysqlUtility;

uses(SunhillDatabaseTestCase::class);

test('getStructureMatrix() for dummy', function()
{
    $scenario = new DummyScenario($this);
    $test = new PoolMysqlUtility($scenario->structure());
    
    $matrix = $test->getStructureMatrix();
    
    expect(isset($matrix['dummies']['dummyint']))->toBe(true);
    expect($matrix['dummies']['dummyint']->type)->toBe('integer');
});

test('getDBMatrix() for dummy', function()
{
    $scenario = new DummyScenario($this);
    $test = new PoolMysqlUtility($scenario->structure());
    
    $matrix = $test->getDBMatrix();
    
    expect(isset($matrix['dummies']['dummyint']))->toBe(true);
    expect($matrix['dummies']['dummyint']->type)->toBe('integer');
});

test('getStructureMatrix() for parentobject', function()
{
    $scenario = new DummyScenario($this);
    $test = new PoolMysqlUtility($scenario->structure());
    
    $matrix = $test->getStructureMatrix();

    excpect(isset($matrix['parentobjects']['parent_string']))->toBe(true);
    expect($matrix['parentobjects']['parent_string']->type)->toBe('string');
});