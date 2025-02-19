<?php

namespace Sunhill\Tests\Unit\Query;

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Query\QueryObject;
use Sunhill\Query\QueryHandler;
use Sunhill\Query\Exceptions\QueryObjectExpectedException;

uses(SunhillTestCase::class);

test("setQueryObject() and getQueryObject()", function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    
    $test = new QueryHandler();
    $test->setQueryObject($query_object);
    
    expect($test->getQueryObject())->toBe($query_object);    
});

it('fails when getQueryObject() is called and not QueryObject is set', function()
{
    $test = new QueryHandler();
    $test->getQueryObject();    
})->throws(QueryObjectExpectedException::class);