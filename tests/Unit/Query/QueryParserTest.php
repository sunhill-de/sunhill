<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Tests\Unit\Parser\Examples\DummyExecutor;
use Sunhill\Facades\Queries;

uses(SunhillTestCase::class);

test('Expression parser', function($input, $output)
{
    $ast = Queries::parseQueryString($input);
    
    $executor = new DummyExecutor();
    expect($executor->execute($ast))->toBe($output);
})->with([
    ['5','5'],
    ['"abc"','"abc"'],
    ['abc','abc'],
    ['a.b','{a}.b'], 
    ['a->b','{a}->b'],
    ['a.b->c','{{a}.b}->c'],
    ['1+2','(1)+(2)'],
    ['1*2','(1)*(2)'],
    ['1+2*3','(1)+((2)*(3))'],
    ['(1+2)*3','((1)+(2))*(3)'],
    ['sin(x)','sin(x)'],
    ['sin(x+2)','sin((x)+(2))'],
    ['sin(x+2)+2','(sin((x)+(2)))+(2)']
]);