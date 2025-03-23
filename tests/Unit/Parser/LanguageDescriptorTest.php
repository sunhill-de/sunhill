<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Tests\Unit\Parser\Examples\DummyLanguage;
use Sunhill\Query\Exceptions\InvalidTokenException;
use Sunhill\Parser\Exceptions\StringNotClosedException;
use Sunhill\Parser\Lexer;

uses(SunhillTestCase::class);

test('Test EOL', function()
{
    $test = new Lexer('');
    $test->loadLanguageDescriptor(new DummyLanguage());
    
    expect($test->getNextToken())->toBe(null);
});

test('Move pointer to next token', function()
{
    $test = new Lexer('1 2');
    $test->loadLanguageDescriptor(new DummyLanguage());
    
    $result = $test->getNextToken();
    $result = $test->getNextToken();
    expect($result->getValue())->toBe('2');
});

test('Test special chars',function($input, $token, $position, $next_pos, $value = null)
{
    $test = new Lexer($input);
    $test->loadLanguageDescriptor(new DummyLanguage());

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
    'boolean (true)'=>['true def','boolean',4,5,true],
    'boolean (false)'=>['false def','boolean',5,6,false],
    'integer'=>['123 def','integer',3,4,'123'],
    'float'=>['1.23 def','float',4,5,'1.23'],
    '"abc"'=>['"abc" def','string',5,6,'abc'],
    "'abc'"=>["'abc' def",'string',5,6,'abc'],
    '"abc\"def"'=>['"abc\"def" def','string',10,11,'abc"def'],
    "'abc\'def'"=>["'abc\'def' def",'string',10,11,"abc'def"],
    '2025-02-25'=>['2025-02-25 def','date',10,11,'2025-02-25'],
    '2025-02-25 02:02:22'=>['2025-02-25 02:02:22 def','datetime',19,20,'2025-02-25 02:02:22'],
    '02:02:22'=>['02:02:22 def','time',8,9,'02:02:22'],
]);

test('Test move pointer and skip multiple whitespaces', function()
{
    $test = new Lexer('abc    def');
    $test->loadLanguageDescriptor(new DummyLanguage());
    
    $test->getNextToken();
    expect($test->getNextToken()->value)->toBe('def');
});

it('fails when string is not closed', function()
{
    $test = new Lexer('"abc def');
    $test->loadLanguageDescriptor(new DummyLanguage());
    
    $test->getNextToken();
})->throws(StringNotClosedException::class);

it('fails when an unknown token is found', function()
{
    $test = new Lexer('§ def');
    $test->loadLanguageDescriptor(new DummyLanguage());
    
    $test->getNextToken();
})->throws(\Sunhill\Parser\Exceptions\InvalidTokenException::class);

test('previewOperator()', function()
{
    $test = new Lexer('abc+');
    $test->loadLanguageDescriptor(new DummyLanguage());
    
    $token = $test->getNextToken();
    expect($test->previewOperator())->toBe('+');
    expect($test->getColumn())->toBe(3);
});

