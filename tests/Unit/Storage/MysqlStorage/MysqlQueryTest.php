<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\Scenarios\DatabaseScenario;
use Sunhill\Storage\MysqlStorages\MysqlQuery;

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