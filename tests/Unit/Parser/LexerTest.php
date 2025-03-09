<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Query\Exceptions\InvalidTokenException;
use Sunhill\Tests\Unit\Parser\Examples\DummyLexer;

uses(SunhillTestCase::class);

test('Test EOL', function()
{
    $test = new DummyLexer('');
    expect($test->getNextToken())->toBe(null);
});

test('Move pointer to next token', function()
{
    $test = new DummyLexer('1 2');
    $result = $test->getNextToken();
    $result = $test->getNextToken();
    expect($result->getValue())->toBe('2');
});

test('Test special chars',function($input, $token, $position, $next_pos, $value = null)
{
    $test = new DummyLexer($input);
    $result = $test->getNextToken();
    expect($result->getSymbol())->toBe($token);
    expect($test->getPointer())->toBe($position);
    if ($value) {
        expect($result->getValue())->toBe($value);
    }
    $next = $test->getNextToken();
    expect($result->getColumn())->toBe(0);
    expect($next->getValue())->toBe('def');
    expect($next->getColumn())->toBe($next_pos);
})->with([
    ['or def','||',2,3],
    ['and def','&&',3,4],
    ['&& def','&&',2,3],
    ['|| def','||',2,3],
    ['+ def','+',1,2],
    ['- def','-',1,2],
    ['/ def','/',1,2],
    ['* def','*',1,2],
    ['( def','(',1,2],
    [') def',')',1,2],
    ['-> def','->',2,3],
    ["->"."\n"."def",'->',2,0],
    'identifier'=>['abc def','ident',3,4,'abc'],
    'identifier with numbers and underscore'=>['abc_d3 def','ident',6,7,'abc_d3'],
    'identifier starting with underscore'=>['_abc def','ident',4,5,'_abc'],
    'integer'=>['123 def','const',3,4,'123','int'],
    'float'=>['1.23 def','const',4,5,'1.23','float'],
    '"abc"'=>['"abc" def','const',5,6,'abc','str'],
    "'abc'"=>["'abc' def",'const',5,6,'abc','str'],
    '"abc\"def"'=>['"abc\"def" def','const',10,11,'abc"def','str'],
    "'abc\'def'"=>["'abc\'def' def",'const',10,11,"abc'def",'str'],
    '2025-02-25'=>['2025-02-25 def','const',10,11,'2025-02-25','date'],
    '2025-02-25 02:02:22'=>['2025-02-25 02:02:22 def','const',19,20,'2025-02-25 02:02:22','datetime'],
    '02:02:22'=>['02:02:22 def','const',8,9,'02:02:22','time'],
]);

test('Test move pointer and skip multiple whitespaces', function()
{
    $test = new DummyLexer('abc    def');
    $test->getNextToken();
    expect($test->getNextToken()->value)->toBe('def');
});

it('fails when string is not closed', function()
{
    $test = new DummyLexer('"abc def');
    $test->getNextToken();
})->throws(InvalidTokenException::class);

test('Detect type hint', function($test, $expect)
{
    $test = new DummyLexer($test);
    $token = $test->getNextToken();
    
    expect($token->getTypeHint())->toBe($expect);
})->with(
    [
        ['"abc"', "string"],
        ['2025-02-25','date'],
        ['2025-02-25 02:02:22','datetime'],
        ['02:02:22','time'],
        ['123','int'],
        ['1.23','float'],
    ]);

test('previewOperator()', function()
{
   $test = new DummyLexer('abc+');
   $token = $test->getNextToken();
   expect($test->previewOperator())->toBe('+');
   expect($test->getColumn())->toBe(3);
});
