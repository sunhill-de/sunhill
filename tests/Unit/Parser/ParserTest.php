<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Parser\Lexer;
use Sunhill\Tests\Unit\Parser\Examples\DummyParser;
use Sunhill\Parser\Token;

uses(SunhillTestCase::class);

test('Simple constant [4]', function()
{
   $lexer = \Mockery::mock(Lexer::class);
   $lexer->shouldReceive('getNextToken')->andReturn(
       (new Token('const'))->setPosition(0,0)->setValue(4)->setTypeHint('int'),
       null
       );
   $lexer->shouldReceive('previewOperator')->andReturn(null);   
   $test = new DummyParser();
   $result = $test->parse($lexer);
   
   expect($result->getType())->toBe('const');
   expect($result->getValue())->toBe(4);
});

test('Simple addition [4+3]', function()
{
    $lexer = \Mockery::mock(Lexer::class);
    $lexer->shouldReceive('getNextToken')->andReturn(
        (new Token('const'))->setPosition(0,0)->setValue(4)->setTypeHint('int'),
        (new Token('+'))->setPosition(1,0),
        (new Token('const'))->setPosition(2,0)->setValue(3)->setTypeHint('int'),
        null
        );
    $lexer->shouldReceive('previewOperator')->andReturn('+','+','+','+',null,null,null,null);
    
    $test = new DummyParser();
    $result = $test->parse($lexer);
    
    expect($result->getType())->toBe('+');
    expect($result->left()->getType())->toBe('const');
    expect($result->left()->getValue())->toBe(4);
    expect($result->right()->getType())->toBe('const');
    expect($result->right()->getValue())->toBe(3);    
});

test('Addition with three summands [4+3+2]', function()
{
    $lexer = \Mockery::mock(Lexer::class);
    $lexer->shouldReceive('getNextToken')->andReturn(
        (new Token('const'))->setPosition(0,0)->setValue(4)->setTypeHint('int'),
        (new Token('+'))->setPosition(1,0),
        (new Token('const'))->setPosition(2,0)->setValue(3)->setTypeHint('int'),
        (new Token('+'))->setPosition(3,0),
        (new Token('const'))->setPosition(4,0)->setValue(2)->setTypeHint('int'),
        null
        );
    $lexer->shouldReceive('previewOperator')->times(12)->andReturn('+','+','+','+','+','+','+','+',null,null,null,null);
    
    $test = new DummyParser();
    $result = $test->parse($lexer);
    
    expect($result->getType())->toBe('+');
    expect($result->left()->getType())->toBe('+');
    expect($result->left()->left()->getType())->toBe('const');
    expect($result->left()->left()->getValue())->toBe(4);
    expect($result->left()->right()->getType())->toBe('const');
    expect($result->left()->right()->getValue())->toBe(3);
    expect($result->right()->getType())->toBe('const');
    expect($result->right()->getValue())->toBe(2);
});

test('Addition with three summands and brackets [4+(3+2)]', function()
{
    $lexer = \Mockery::mock(Lexer::class);
    $lexer->shouldReceive('getNextToken')->andReturn(
        (new Token('const'))->setPosition(0,0)->setValue(4)->setTypeHint('int'),
        (new Token('+'))->setPosition(1,0),
        (new Token('('))->setPosition(2,0),
        (new Token('const'))->setPosition(3,0)->setValue(3)->setTypeHint('int'),
        (new Token('+'))->setPosition(4,0),
        (new Token('const'))->setPosition(5,0)->setValue(2)->setTypeHint('int'),
        (new Token(')'))->setPosition(6,0),
        null
        );
    $lexer->shouldReceive('previewOperator')->times(16)->andReturn('+','+','+','+','+','+','+','+',null,null,null,null,null,null,null,null);
    
    $test = new DummyParser();
    $result = $test->parse($lexer);
    
    expect($result->getType())->toBe('+');
    expect($result->left()->getType())->toBe('const');
    expect($result->left()->getValue())->toBe(4);
    expect($result->right()->left()->getType())->toBe('const');
    expect($result->right()->left()->getValue())->toBe(3);
    expect($result->right()->right()->getType())->toBe('const');
    expect($result->right()->right()->getValue())->toBe(2);
});
test('Simple product [4*3]', function()
{
    $lexer = \Mockery::mock(Lexer::class);
    $lexer->shouldReceive('getNextToken')->andReturn(
        (new Token('const'))->setPosition(0,0)->setValue(4)->setTypeHint('int'),
        (new Token('*'))->setPosition(1,0),
        (new Token('const'))->setPosition(2,0)->setValue(3)->setTypeHint('int'),
        null
        );
    $lexer->shouldReceive('previewOperator')->times(8)->andReturn('*','*','*','*',null,null,null,null);
    
    $test = new DummyParser();
    $result = $test->parse($lexer);
    
    expect($result->getType())->toBe('*');
    expect($result->left()->getType())->toBe('const');
    expect($result->left()->getValue())->toBe(4);
    expect($result->right()->getType())->toBe('const');
    expect($result->right()->getValue())->toBe(3);
});

test('Multiplication with three factors [4*3*2]', function()
{
    $lexer = \Mockery::mock(Lexer::class);
    $lexer->shouldReceive('getNextToken')->andReturn(
        (new Token('const'))->setPosition(0,0)->setValue(4)->setTypeHint('int'),
        (new Token('*'))->setPosition(1,0),
        (new Token('const'))->setPosition(2,0)->setValue(3)->setTypeHint('int'),
        (new Token('*'))->setPosition(3,0),
        (new Token('const'))->setPosition(4,0)->setValue(2)->setTypeHint('int'),
        null
        );
    $lexer->shouldReceive('previewOperator')->times(12)->andReturn('*','*','*','*','*','*','*','*',null,null,null,null);
    
    $test = new DummyParser();
    $result = $test->parse($lexer);
    
    expect($result->getType())->toBe('*');
    expect($result->left()->getType())->toBe('*');
    expect($result->left()->left()->getType())->toBe('const');
    expect($result->left()->left()->getValue())->toBe(4);
    expect($result->left()->right()->getType())->toBe('const');
    expect($result->left()->right()->getValue())->toBe(3);
    expect($result->right()->getType())->toBe('const');
    expect($result->right()->getValue())->toBe(2);
});

test('Sum with product left [4*3+2]', function()
{
    $lexer = \Mockery::mock(Lexer::class);
    $lexer->shouldReceive('getNextToken')->andReturn(
        (new Token('const'))->setPosition(0,0)->setValue(4)->setTypeHint('int'),
        (new Token('*'))->setPosition(1,0),
        (new Token('const'))->setPosition(2,0)->setValue(3)->setTypeHint('int'),
        (new Token('+'))->setPosition(3,0),
        (new Token('const'))->setPosition(4,0)->setValue(2)->setTypeHint('int'),
        null
        );
    $lexer->shouldReceive('previewOperator')->times(12)->andReturn('*','*','*','*','+','+','+','+',null,null,null,null);
    
    $test = new DummyParser();
    $result = $test->parse($lexer);
    
    expect($result->getType())->toBe('+');
    expect($result->left()->getType())->toBe('*');
    expect($result->left()->left()->getType())->toBe('const');
    expect($result->left()->left()->getValue())->toBe(4);
    expect($result->left()->right()->getType())->toBe('const');
    expect($result->left()->right()->getValue())->toBe(3);
    expect($result->right()->getType())->toBe('const');
    expect($result->right()->getValue())->toBe(2);
});

test('Sum with product right [4+3*2]', function()
{
    $lexer = \Mockery::mock(Lexer::class);
    $lexer->shouldReceive('getNextToken')->andReturn(
        (new Token('const'))->setPosition(0,0)->setValue(4)->setTypeHint('int'),
        (new Token('+'))->setPosition(1,0),
        (new Token('const'))->setPosition(2,0)->setValue(3)->setTypeHint('int'),
        (new Token('*'))->setPosition(3,0),
        (new Token('const'))->setPosition(4,0)->setValue(2)->setTypeHint('int'),
        null
        );
    $lexer->shouldReceive('previewOperator')->times(12)->andReturn('+','+','+','+','*','*','*','*',null,null,null,null);
    
    $test = new DummyParser();
    $result = $test->parse($lexer);
    
    expect($result->getType())->toBe('+');
    expect($result->left()->getType())->toBe('const');
    expect($result->left()->getValue())->toBe(4);
    expect($result->right()->getType())->toBe('*');
    expect($result->right()->left()->getType())->toBe('const');
    expect($result->right()->left()->getValue())->toBe(3);
    expect($result->right()->right()->type)->toBe('const');
    expect($result->right()->right()->getValue())->toBe(2);
});


test('Product with sum in brackets (4*(3+2))', function()
{
    $lexer = \Mockery::mock(Lexer::class);
    $lexer->shouldReceive('getNextToken')->andReturn(
        (new Token('const'))->setPosition(0,0)->setValue(4)->setTypeHint('int'),
        (new Token('*'))->setPosition(1,0),
        (new Token('('))->setPosition(2,0),
        (new Token('const'))->setPosition(3,0)->setValue(3)->setTypeHint('int'),
        (new Token('+'))->setPosition(4,0),
        (new Token('const'))->setPosition(5,0)->setValue(2)->setTypeHint('int'),
        (new Token(')'))->setPosition(6,0),
        null
        );
    $lexer->shouldReceive('previewOperator')->times(16)->andReturn('*','*','*','*','+','+','+','+',null,null,null,null,null,null,null,null);
    
    $test = new DummyParser();
    $result = $test->parse($lexer);
    
    expect($result->getType())->toBe('*');
    expect($result->left()->getType())->toBe('const');
    expect($result->left()->getValue())->toBe(4);
    expect($result->right()->getType())->toBe('+');
    expect($result->right()->left()->getType())->toBe('const');
    expect($result->right()->left()->getValue())->toBe(3);
    expect($result->right()->right()->getType())->toBe('const');
    expect($result->right()->right()->getValue())->toBe(2);
});

test('Simple unary minus [-4]', function()
{
    $lexer = \Mockery::mock(Lexer::class);
    $lexer->shouldReceive('getNextToken')->andReturn(
        (new Token('-'))->setPosition(1,0),
        (new Token('const'))->setPosition(0,0)->setValue(4)->setTypeHint('int'),
        null
        );
    $lexer->shouldReceive('previewOperator')->times(4)->andReturn(null);
    
    $test = new DummyParser();
    $result = $test->parse($lexer);
    
    expect($result->getType())->toBe('u-');
    expect($result->child()->getType())->toBe('const');
    expect($result->child()->getValue())->toBe(4);    
});

test('Simple addition with unary minus [4+-3]', function()
{
    $lexer = \Mockery::mock(Lexer::class);
    $lexer->shouldReceive('getNextToken')->andReturn(
        (new Token('const'))->setPosition(0,0)->setValue(4)->setTypeHint('int'),
        (new Token('+'))->setPosition(1,0),
        (new Token('-'))->setPosition(2,0),
        (new Token('const'))->setPosition(3,0)->setValue(3)->setTypeHint('int'),
        null
        );
    $lexer->shouldReceive('previewOperator')->andReturn('+','+','+','+',null,null,null,null);
    
    $test = new DummyParser();
    $result = $test->parse($lexer);
    
    expect($result->getType())->toBe('+');
    expect($result->left()->getType())->toBe('const');
    expect($result->left()->getValue())->toBe(4);
    expect($result->right()->getType())->toBe('u-');
    expect($result->right()->child()->getType())->toBe('const');
    expect($result->right()->child()->getValue())->toBe(3);
});

test('Simple function [sin(3)]', function()
{
    $lexer = \Mockery::mock(Lexer::class);
    $lexer->shouldReceive('getNextToken')->andReturn(
        (new Token('ident'))->setPosition(0,0)->setValue('sin'),
        (new Token('('))->setPosition(3,0),
        (new Token('const'))->setPosition(3,0)->setValue(3)->setTypeHint('int'),
        (new Token(')'))->setPosition(4,0),
        null
        );
    $lexer->shouldReceive('previewOperator')->andReturn('(',')',')',')',')',null,null,null,null);
    
    $test = new DummyParser();
    $result = $test->parse($lexer);
    
    expect($result->getType())->toBe('func');
    expect($result->name())->toBe('sin');
    expect($result->arguments()->getType())->toBe('const');
    expect($result->arguments()->getValue())->toBe(3);
});

test('Function in sum [sin(4)+3]', function()
{
    $lexer = \Mockery::mock(Lexer::class);
    $lexer->shouldReceive('getNextToken')->andReturn(
        (new Token('ident'))->setPosition(0,0)->setValue('sin'),
        (new Token('('))->setPosition(3,0),
        (new Token('const'))->setPosition(3,0)->setValue(4)->setTypeHint('int'),
        (new Token(')'))->setPosition(4,0),
        (new Token('+'))->setPosition(5,0),
        (new Token('const'))->setPosition(6,0)->setValue(3),
        null
        );
    $lexer->shouldReceive('previewOperator')->andReturn('(',')',')',')',')',null,null,null,null);
    
    $test = new DummyParser();
    $result = $test->parse($lexer);
    
    expect($result->getType())->toBe('+');
    expect($result->left()->name())->toBe('sin');
    expect($result->left()->arguments()->getType())->toBe('const');
    expect($result->left()->arguments()->getValue())->toBe(4);
    expect($result->right()->getType())->toBe('const');
    expect($result->right()->getValue())->toBe(3);
});

