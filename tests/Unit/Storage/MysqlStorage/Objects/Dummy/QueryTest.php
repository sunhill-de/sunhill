<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Query\QueryParser\QueryNode;

uses(SunhillDatabaseTestCase::class);

test('Query a dummy', function($callback, $expect, $manipulator = null)
{
    $test = new MysqlObjectStorage();
    $test->setStructure(Dummy::getExpectedStructure());
    Dummy::prepareDatabase($this);
    
    $test->setTargetSubid('dummies');
    $query = new QueryNode();
    $result = $callback($query);
    if (is_callable($manipulator)) {
        $result = $manipulator($test->executeQuery($result));
    }
    expect($result)->toBe($expect);
})->with([
    'simple count()'=>
    [
        function(QueryNode $query) 
        { 
            return $query->verb('count'); 
        }, 10
     ],
    'simple get()'=>[function($query) { return $query->get(); }, 345, function($result) { return $result[2]->dummyint; }],
    'simple first()'=>[function($query) { return $query->first(); }, 123, function($result) { return $result->dummyint; }],
    'count() with where 1'=>[function($query) { return $query->where('dummyint',123)->count(); },2],
    'count() with where 2'=>[function($query) { return $query->where('dummyint','>',345)->count(); },6],
    'count() with 2xwhere'=>[function($query) { return $query->where('dummyint','>',123)->where('dummyint','<',555)->count(); },3],
    'count() with orWhere'=>[function($query) { return $query->where('dummyint','=',123)->orWhere('dummyint','=',345)->count(); },3],
    'count() with whereNot'=>[function($query) { return $query->where('dummyint','>',123)->whereNot('dummyint','>',900)->count(); },4],
    'count() with orWhereNot'=>[function($query) { return $query->where('dummyint','>',456)->orWhereNot('dummyint','<',222)->count(); },8],
    ]);
