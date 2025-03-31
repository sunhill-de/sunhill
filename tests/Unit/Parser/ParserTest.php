<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Parser\Lexer;
use Sunhill\Tests\Unit\Parser\Examples\DummyParser;
use Sunhill\Parser\Token;
use Sunhill\Query\Exceptions\InvalidStatementException;
use Sunhill\Parser\Exceptions\InputNotParsableException;
use Sunhill\Tests\Unit\Parser\Examples\DummyExecutor;

uses(SunhillTestCase::class);

test('Simple integer [4]', function()
{
   $lexer = \Mockery::mock(Lexer::class);
   $lexer->shouldReceive('getNextToken')->andReturn(
       (new Token('integer'))->setPosition(0,0)->setValue(4)->setTypeHint('int'),
       null
       );
   $lexer->shouldReceive('previewOperator')->andReturn(null);   
   $test = new DummyParser();
   $result = $test->parse($lexer);
   
   $executor = new DummyExecutor();
   expect($executor->execute($result))->toBe("4");
   
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
    
    $test = new DummyParser();
    $result = $test->parse($lexer);
   
    $executor = new DummyExecutor();
    expect($executor->execute($result))->toBe("(4)+(3)");
    
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
    
    $test = new DummyParser();
    $result = $test->parse($lexer);
    
    $executor = new DummyExecutor();
    expect($executor->execute($result))->toBe("((4)+(3))+(2)");
    
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
    
    $test = new DummyParser();
    $result = $test->parse($lexer);
    
    $executor = new DummyExecutor();
    expect($executor->execute($result))->toBe("(4)+((3)+(2))");
    
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
    
    $test = new DummyParser();
    $result = $test->parse($lexer);
    
    $executor = new DummyExecutor();
    expect($executor->execute($result))->toBe("(4)*(3)");
    
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
    
    $test = new DummyParser();
    $result = $test->parse($lexer);
    
    $executor = new DummyExecutor();
    expect($executor->execute($result))->toBe("((4)*(3))*(2)");
    
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
    
    
    $test = new DummyParser();
    $result = $test->parse($lexer);
    
    $executor = new DummyExecutor();
    expect($executor->execute($result))->toBe("((4)*(3))+(2)");
    
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
    
    $test = new DummyParser();
    $result = $test->parse($lexer);
    
    $executor = new DummyExecutor();
    expect($executor->execute($result))->toBe("(4)+((3)*(2))");
    
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
    
    $test = new DummyParser();
    $result = $test->parse($lexer);
    
    $executor = new DummyExecutor();
    expect($executor->execute($result))->toBe("(4)*((3)+(2))");
    
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
    
    $test = new DummyParser();
    $result = $test->parse($lexer);
    
    $executor = new DummyExecutor();
    expect($executor->execute($result))->toBe("u-(4)");
    
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
    
    $test = new DummyParser();
    $result = $test->parse($lexer);
    
    $executor = new DummyExecutor();
    expect($executor->execute($result))->toBe("(4)+(u-(3))");
    
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
    
    $test = new DummyParser();
    $result = $test->parse($lexer);
    
    $executor = new DummyExecutor();
    expect($executor->execute($result))->toBe("sin(3)");
    
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
    
    $test = new DummyParser();
    $result = $test->parse($lexer);
    
    $executor = new DummyExecutor();
    expect($executor->execute($result))->toBe("sin()");
    
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
    
    $test = new DummyParser();
    $result = $test->parse($lexer);
    
    $executor = new DummyExecutor();
    expect($executor->execute($result))->toBe("(sin(4))+(3)");
    
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
    
    $test = new DummyParser();
    $test->parse($lexer);    
})->throws(InputNotParsableException::class);
