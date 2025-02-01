<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\Scenarios\DatabaseScenario;
use Sunhill\Storage\MysqlStorage\MysqlQuery;

uses(SunhillDatabaseTestCase::class);

test('select query over one table', function()
{
    $scenario = new DatabaseScenario($this);
    $scenario->migrate();
    $scenario->seed();
    
    $test = new MysqlQuery('tableA');
    $test = $test->orderBy('value');
    $list = $test->first();
    
    expect($list->id)->toBe(4);
});

test('select query over one table with where', function()
{
    $scenario = new DatabaseScenario($this);
    $scenario->migrate();
    $scenario->seed();
    
    $test = new MysqlQuery('tableA');
    $test = $test->where('value','>',200)->orderBy('value');
    $list = $test->first();
    
    expect($list->id)->toBe(1);
});

test('select query with one default join', function()
{
    $scenario = new DatabaseScenario($this);
    $scenario->migrate();
    $scenario->seed();
    
    $test = new MysqlQuery('tableA');
    $list = $test->join('tableB')->orderBy('value')->first();
    
    expect($list->Bvalue)->toBe(123);
});

test('select query with one individual join', function()
{
    $scenario = new DatabaseScenario($this);
    $scenario->migrate();
    $scenario->seed();
    
    $test = new MysqlQuery('tableA');
    $list = $test->join('tableB','link_to_tableB')->orderBy('value')->first();
    
    expect($list->Bvalue)->toBe(123);
});

test('select query with one individual join and where', function()
{
    $scenario = new DatabaseScenario($this);
    $scenario->migrate();
    $scenario->seed();
    
    $test = new MysqlQuery('tableA');
    $list = $test->join('tableB','link_to_tableB')->where('value','>',200)->orderBy('value','desc')->first();
    
    expect($list->Bvalue)->toBe(400);
});


test('select query with one individual join with two non default names and where', function()
{
    $scenario = new DatabaseScenario($this);
    $scenario->migrate();
    $scenario->seed();
    
    $test = new MysqlQuery('tableA');
    $list = $test->join('tableC','link_to_tableB','other_id')->where('value','>',200)->orderBy('value','desc')->first();
    
    expect($list->Cvalue)->toBe(123);
});



