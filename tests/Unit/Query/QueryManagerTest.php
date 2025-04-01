<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Facades\Query;
use Sunhill\Tests\Unit\Parser\Examples\DummyExecutor;

uses(SunhillTestCase::class);

test('parseQueryString parses an expression', function()
{
   $ast = Query::parseQueryString('5+4');
   
   $executor = new DummyExecutor();
   expect($executor->execute($ast))->toBe('(5)+(4)');
});


test('parseQueryString parses an string that is no expression', function()
{
    $ast = Query::parseQueryString('5+4+');
    
    $executor = new DummyExecutor();
    expect($executor->execute($ast))->toBe('"5+4+"');
});