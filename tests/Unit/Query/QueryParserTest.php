<?php

/**
 * @file QueryParserTest.php
 * tests: /src/Query/QueryParser.php
 * free of dependent units: yes
 */

use Sunhill\Facades\Queries;
use Sunhill\Tests\SunhillLaravelTestCase;
use Sunhill\Tests\Unit\Parser\Examples\DummyExecutor;

uses(SunhillLaravelTestCase::class);

test('Expression parser', function ($input, $output) {
    $ast = Queries::parseQueryString($input);

    $executor = new DummyExecutor;
    expect($executor->execute($ast))->toBe($output);
})->with([
    ['5', '5'],
    ['"abc"', '"abc"'],
    ['abc', 'abc'],
    ['a.b', '{a}.b'],
    ['a->b', '{a}->b'],
    ['a.b->c', '{{a}.b}->c'],
    ['1+2', '(1)+(2)'],
    ['1*2', '(1)*(2)'],
    ['1+2*3', '(1)+((2)*(3))'],
    ['(1+2)*3', '((1)+(2))*(3)'],
    ['sin(x)', 'sin({x})'],
    ['sin(x+2)', 'sin({(x)+(2)})'],
    ['sin(x+2)+2', '(sin({(x)+(2)}))+(2)'],
    ['[1,2,3]', '[{1},{2},{3}]'],
    ['[1]', '[{1}]'],
    ['[1+ident]', '[{(1)+(ident)}]'],
    ['a as alias', '{a} as {alias}'],
    ['sin(a)+2 as alias', '{(sin({a}))+(2)} as {alias}'],
    ['a.b->c as alias', '{{{a}.b}->c} as {alias}'],
]);
