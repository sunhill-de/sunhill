<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Tests\Unit\Parser\Examples\DummyLanguage;
use Sunhill\Query\Exceptions\InvalidTokenException;
use Sunhill\Parser\Exceptions\StringNotClosedException;
use Sunhill\Parser\Lexer;
use Sunhill\Parser\Parser;
use Sunhill\Parser\Token;
use Sunhill\Parser\Exceptions\InputNotParsableException;

uses(SunhillTestCase::class);

// ======================================= Lexer tests =================================================
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

// ============================================= Parser tests =====================================================
test('Simple integer [4]', function()
{
    $lexer = \Mockery::mock(Lexer::class);
    $lexer->shouldReceive('getNextToken')->andReturn(
        (new Token('integer'))->setPosition(0,0)->setValue(4)->setTypeHint('int'),
        null
        );
    $lexer->shouldReceive('previewOperator')->andReturn(null);
    $test = new Parser();
    $test->loadLanguageDescriptor(new DummyLanguage());
    
    $result = $test->parse($lexer);
    
    expect($result->getType())->toBe('integer');
    expect($result->getValue())->toBe(4);
});

test('Simple addition [4+3]', function()
{
    $lexer = \Mockery::mock(Lexer::class);
    $lexer->shouldReceive('getNextToken')->andReturn(
        (new Token('integer'))->setPosition(0,0)->setValue(4)->setTypeHint('int'),
        (new Token('+'))->setPosition(1,0),
        (new Token('integer'))->setPosition(2,0)->setValue(3)->setTypeHint('int'),
        null
        );
    $lexer->shouldReceive('previewOperator')->andReturn('+','+','+','+','+',null,null,null,null,null);
    
    $test = new Parser();
    $test->loadLanguageDescriptor(new DummyLanguage());
    
    $result = $test->parse($lexer);
    
    expect($result->getType())->toBe('+');
    expect($result->left()->getType())->toBe('integer');
    expect($result->left()->getValue())->toBe(4);
    expect($result->right()->getType())->toBe('integer');
    expect($result->right()->getValue())->toBe(3);
});

test('Addition with three summands [4+3+2]', function()
{
    $lexer = \Mockery::mock(Lexer::class);
    $lexer->shouldReceive('getNextToken')->andReturn(
        (new Token('integer'))->setPosition(0,0)->setValue(4),
        (new Token('+'))->setPosition(1,0),
        (new Token('integer'))->setPosition(2,0)->setValue(3),
        (new Token('+'))->setPosition(3,0),
        (new Token('integer'))->setPosition(4,0)->setValue(2),
        null
        );
    $lexer->shouldReceive('previewOperator')->times(14)->andReturn('+','+','+','+','+','+','+','+','+','+',null,null,null);
    
    $test = new Parser();
    $test->loadLanguageDescriptor(new DummyLanguage());
    $result = $test->parse($lexer);
    
    expect($result->getType())->toBe('+');
    expect($result->left()->getType())->toBe('+');
    expect($result->left()->left()->getType())->toBe('integer');
    expect($result->left()->left()->getValue())->toBe(4);
    expect($result->left()->right()->getType())->toBe('integer');
    expect($result->left()->right()->getValue())->toBe(3);
    expect($result->right()->getType())->toBe('integer');
    expect($result->right()->getValue())->toBe(2);
});

test('Addition with three summands and brackets [4+(3+2)]', function()
{
    $lexer = \Mockery::mock(Lexer::class);
    $lexer->shouldReceive('getNextToken')->andReturn(
        (new Token('integer'))->setPosition(0,0)->setValue(4)->setTypeHint('int'),
        (new Token('+'))->setPosition(1,0),
        (new Token('('))->setPosition(2,0),
        (new Token('integer'))->setPosition(3,0)->setValue(3)->setTypeHint('int'),
        (new Token('+'))->setPosition(4,0),
        (new Token('integer'))->setPosition(5,0)->setValue(2)->setTypeHint('int'),
        (new Token(')'))->setPosition(6,0),
        null
        );
    $lexer->shouldReceive('previewOperator')->times(17)->andReturn('+','+','+','+','+','+','+','+',null,null,null,null,null,null);
    
    $test = new Parser();
    $test->loadLanguageDescriptor(new DummyLanguage());
    $result = $test->parse($lexer);
    
    expect($result->getType())->toBe('+');
    expect($result->left()->getType())->toBe('integer');
    expect($result->left()->getValue())->toBe(4);
    expect($result->right()->left()->getType())->toBe('integer');
    expect($result->right()->left()->getValue())->toBe(3);
    expect($result->right()->right()->getType())->toBe('integer');
    expect($result->right()->right()->getValue())->toBe(2);
});
test('Simple product [4*3]', function()
{
    $lexer = \Mockery::mock(Lexer::class);
    $lexer->shouldReceive('getNextToken')->andReturn(
        (new Token('integer'))->setPosition(0,0)->setValue(4)->setTypeHint('int'),
        (new Token('*'))->setPosition(1,0),
        (new Token('integer'))->setPosition(2,0)->setValue(3)->setTypeHint('int'),
        null
        );
    $lexer->shouldReceive('previewOperator')->times(9)->andReturn('*','*','*','*','*',null,null,null);
    
    $test = new Parser();
    $test->loadLanguageDescriptor(new DummyLanguage());
    $result = $test->parse($lexer);
    
    expect($result->getType())->toBe('*');
    expect($result->left()->getType())->toBe('integer');
    expect($result->left()->getValue())->toBe(4);
    expect($result->right()->getType())->toBe('integer');
    expect($result->right()->getValue())->toBe(3);
});

test('Multiplication with three factors [4*3*2]', function()
{
    $lexer = \Mockery::mock(Lexer::class);
    $lexer->shouldReceive('getNextToken')->andReturn(
        (new Token('integer'))->setPosition(0,0)->setValue(4)->setTypeHint('int'),
        (new Token('*'))->setPosition(1,0),
        (new Token('integer'))->setPosition(2,0)->setValue(3)->setTypeHint('int'),
        (new Token('*'))->setPosition(3,0),
        (new Token('integer'))->setPosition(4,0)->setValue(2)->setTypeHint('int'),
        null
        );
    $lexer->shouldReceive('previewOperator')->times(14)->andReturn('*','*','*','*','*','*','*','*','*','*',null,null,null);
    
    $test = new Parser();
    $test->loadLanguageDescriptor(new DummyLanguage());
    $result = $test->parse($lexer);
    
    expect($result->getType())->toBe('*');
    expect($result->left()->getType())->toBe('*');
    expect($result->left()->left()->getType())->toBe('integer');
    expect($result->left()->left()->getValue())->toBe(4);
    expect($result->left()->right()->getType())->toBe('integer');
    expect($result->left()->right()->getValue())->toBe(3);
    expect($result->right()->getType())->toBe('integer');
    expect($result->right()->getValue())->toBe(2);
});

test('Sum with product left [4*3+2]', function()
{
    $lexer = \Mockery::mock(Lexer::class);
    $lexer->shouldReceive('getNextToken')->andReturn(
        (new Token('integer'))->setPosition(0,0)->setValue(4)->setTypeHint('int'),
        (new Token('*'))->setPosition(1,0),
        (new Token('integer'))->setPosition(2,0)->setValue(3)->setTypeHint('int'),
        (new Token('+'))->setPosition(3,0),
        (new Token('integer'))->setPosition(4,0)->setValue(2)->setTypeHint('int'),
        null
        );
    $lexer->shouldReceive('previewOperator')->times(14)->andReturn('*','*','*','*','*','+','+','+','+','+',null,null,null);
    
    
    $test = new Parser();
    $test->loadLanguageDescriptor(new DummyLanguage());
    $result = $test->parse($lexer);
    
    expect($result->getType())->toBe('+');
    expect($result->left()->getType())->toBe('*');
    expect($result->left()->left()->getType())->toBe('integer');
    expect($result->left()->left()->getValue())->toBe(4);
    expect($result->left()->right()->getType())->toBe('integer');
    expect($result->left()->right()->getValue())->toBe(3);
    expect($result->right()->getType())->toBe('integer');
    expect($result->right()->getValue())->toBe(2);
});

test('Sum with product right [4+3*2]', function()
{
    $lexer = \Mockery::mock(Lexer::class);
    $lexer->shouldReceive('getNextToken')->andReturn(
        (new Token('integer'))->setPosition(0,0)->setValue(4)->setTypeHint('int'),
        (new Token('+'))->setPosition(1,0),
        (new Token('integer'))->setPosition(2,0)->setValue(3)->setTypeHint('int'),
        (new Token('*'))->setPosition(3,0),
        (new Token('integer'))->setPosition(4,0)->setValue(2)->setTypeHint('int'),
        null
        );
    $lexer->shouldReceive('previewOperator')->times(15)->andReturn('+','+','+','+','+','*','*','*','*','*',null,null,null,null);
    
    $test = new Parser();
    $test->loadLanguageDescriptor(new DummyLanguage());
    $result = $test->parse($lexer);
    
    expect($result->getType())->toBe('+');
    expect($result->left()->getType())->toBe('integer');
    expect($result->left()->getValue())->toBe(4);
    expect($result->right()->getType())->toBe('*');
    expect($result->right()->left()->getType())->toBe('integer');
    expect($result->right()->left()->getValue())->toBe(3);
    expect($result->right()->right()->type)->toBe('integer');
    expect($result->right()->right()->getValue())->toBe(2);
});


test('Product with sum in brackets (4*(3+2))', function()
{
    $lexer = \Mockery::mock(Lexer::class);
    $lexer->shouldReceive('getNextToken')->andReturn(
        (new Token('integer'))->setPosition(0,0)->setValue(4)->setTypeHint('int'),
        (new Token('*'))->setPosition(1,0),
        (new Token('('))->setPosition(2,0),
        (new Token('integer'))->setPosition(3,0)->setValue(3)->setTypeHint('int'),
        (new Token('+'))->setPosition(4,0),
        (new Token('integer'))->setPosition(5,0)->setValue(2)->setTypeHint('int'),
        (new Token(')'))->setPosition(6,0),
        null
        );
    $lexer->shouldReceive('previewOperator')->times(17)->andReturn('*','*','*','*','*','+','+','+','+','+',null,null,null,null,null,null);
    
    $test = new Parser();
    $test->loadLanguageDescriptor(new DummyLanguage());
    $result = $test->parse($lexer);
    
    expect($result->getType())->toBe('*');
    expect($result->left()->getType())->toBe('integer');
    expect($result->left()->getValue())->toBe(4);
    expect($result->right()->getType())->toBe('+');
    expect($result->right()->left()->getType())->toBe('integer');
    expect($result->right()->left()->getValue())->toBe(3);
    expect($result->right()->right()->getType())->toBe('integer');
    expect($result->right()->right()->getValue())->toBe(2);
});

test('Simple unary minus [-4]', function()
{
    $lexer = \Mockery::mock(Lexer::class);
    $lexer->shouldReceive('getNextToken')->andReturn(
        (new Token('-'))->setPosition(1,0),
        (new Token('integer'))->setPosition(0,0)->setValue(4)->setTypeHint('int'),
        null
        );
    $lexer->shouldReceive('previewOperator')->times(4)->andReturn(null);
    
    $test = new Parser();
    $test->loadLanguageDescriptor(new DummyLanguage());
    $result = $test->parse($lexer);
    
    expect($result->getType())->toBe('u-');
    expect($result->child()->getType())->toBe('integer');
    expect($result->child()->getValue())->toBe(4);
});

test('Simple addition with unary minus [4+-3]', function()
{
    $lexer = \Mockery::mock(Lexer::class);
    $lexer->shouldReceive('getNextToken')->andReturn(
        (new Token('integer'))->setPosition(0,0)->setValue(4)->setTypeHint('int'),
        (new Token('+'))->setPosition(1,0),
        (new Token('-'))->setPosition(2,0),
        (new Token('integer'))->setPosition(3,0)->setValue(3)->setTypeHint('int'),
        null
        );
    $lexer->shouldReceive('previewOperator')->andReturn('+','+','+','+',null,null,null,null);
    
    $test = new Parser();
    $test->loadLanguageDescriptor(new DummyLanguage());
    $result = $test->parse($lexer);
    
    expect($result->getType())->toBe('+');
    expect($result->left()->getType())->toBe('integer');
    expect($result->left()->getValue())->toBe(4);
    expect($result->right()->getType())->toBe('u-');
    expect($result->right()->child()->getType())->toBe('integer');
    expect($result->right()->child()->getValue())->toBe(3);
});

test('Simple function [sin(3)]', function()
{
    $lexer = \Mockery::mock(Lexer::class);
    $lexer->shouldReceive('getNextToken')->andReturn(
        (new Token('ident'))->setPosition(0,0)->setValue('sin'),
        (new Token('('))->setPosition(3,0),
        (new Token('integer'))->setPosition(4,0)->setValue(3)->setTypeHint('int'),
        (new Token(')'))->setPosition(5,0),
        null
        );
    $lexer->shouldReceive('previewOperator')->andReturn('(',')',')',')',')',null,null,null,null);
    
    $test = new Parser();
    $test->loadLanguageDescriptor(new DummyLanguage());
    $result = $test->parse($lexer);
    
    expect($result->getType())->toBe('func');
    expect($result->name())->toBe('sin');
    expect($result->arguments()->getType())->toBe('integer');
    expect($result->arguments()->getValue())->toBe(3);
});

test('Simple function with no argument [sin()]', function()
{
    $lexer = \Mockery::mock(Lexer::class);
    $lexer->shouldReceive('getNextToken')->andReturn(
        (new Token('ident'))->setPosition(0,0)->setValue('sin'),
        (new Token('('))->setPosition(3,0),
        (new Token(')'))->setPosition(4,0),
        null
        );
    $lexer->shouldReceive('previewOperator')->andReturn('(',')',')',')',')',null,null,null,null);
    
    $test = new Parser();
    $test->loadLanguageDescriptor(new DummyLanguage());
    $result = $test->parse($lexer);
    
    expect($result->getType())->toBe('func');
    expect($result->name())->toBe('sin');
    expect($result->arguments())->toBe(null);
});

test('Function in sum [sin(4)+3]', function()
{
    $lexer = \Mockery::mock(Lexer::class);
    $lexer->shouldReceive('getNextToken')->andReturn(
        (new Token('ident'))->setPosition(0,0)->setValue('sin'),
        (new Token('('))->setPosition(3,0),
        (new Token('integer'))->setPosition(3,0)->setValue(4)->setTypeHint('int'),
        (new Token(')'))->setPosition(4,0),
        (new Token('+'))->setPosition(5,0),
        (new Token('integer'))->setPosition(6,0)->setValue(3),
        null
        );
    $lexer->shouldReceive('previewOperator')->andReturn('(',')',')',')',')',null,null,null,null);
    
    $test = new Parser();
    $test->loadLanguageDescriptor(new DummyLanguage());
    $result = $test->parse($lexer);
    
    expect($result->getType())->toBe('+');
    expect($result->left()->name())->toBe('sin');
    expect($result->left()->arguments()->getType())->toBe('integer');
    expect($result->left()->arguments()->getValue())->toBe(4);
    expect($result->right()->getType())->toBe('integer');
    expect($result->right()->getValue())->toBe(3);
});

it('Fails when an unexpected token comes', function()
{
    $lexer = \Mockery::mock(Lexer::class);
    $lexer->shouldReceive('getNextToken')->andReturn(
        (new Token('ident'))->setPosition(0,0)->setValue('sin'),
        (new Token('('))->setPosition(3,0),
        (new Token('+'))->setPosition(4,0),
        null
        );
    $lexer->shouldReceive('previewOperator')->andReturn('(',')',')',')',')',null,null,null,null);
    
    $test = new Parser();
    $test->loadLanguageDescriptor(new DummyLanguage());
    $test->parse($lexer);
})->throws(InputNotParsableException::class);
