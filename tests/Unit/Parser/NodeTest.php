<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Parser\Nodes\BooleanNode;
use Sunhill\Parser\Nodes\IntegerNode;
use Sunhill\Parser\Nodes\FloatNode;
use Sunhill\Parser\Nodes\StringNode;
use Sunhill\Parser\Nodes\IdentifierNode;
use Sunhill\Parser\Nodes\FunctionNode;
use Sunhill\Parser\Nodes\ArrayNode;
use Sunhill\Parser\Nodes\UnaryNode;
use Sunhill\Parser\Nodes\BinaryNode;
use Sunhill\Parser\Nodes\DateNode;
use Sunhill\Parser\Nodes\DateTimeNode;
use Sunhill\Parser\Nodes\TimeNode;
use Sunhill\Parser\Exceptions\AnalyzerException;
use Sunhill\Parser\LanguageDescriptor\FunctionDescriptor;

uses(SunhillTestCase::class);

test('getDatatype()', function($modifier, $expect)
{
    expect($modifier()->getDatatype())->toBe($expect);
})->with(
[
    'BooleanNode'=>[function() { return new BooleanNode(true); }, 'boolean'],
    'IntegerNode'=>[function() { return new IntegerNode(10); }, 'integer'],
    'FloatNode'=>[function() { return new FloatNode(1.23); }, 'float'],
    'StringNode'=>[function() { return new StringNode('abc'); }, 'string'],
    'DateNode'=>[function() { return new DateNode('2025-07-02'); }, 'date'],
    'DateTimeNode'=>[function() { return new DateTimeNode('2025-07-02 11:12:13'); }, 'datetime'],
    'TimeNode'=>[function() { return new TimeNode('11:12:13'); }, 'time'],
    'IdentifierNode (type set)'=>[function() 
    { 
        $return = new IdentifierNode('test');
        $return->setDatatype('integer');
        return  $return;
    }, 'integer'],
    'IdentifierNode (type not set)'=>[function() { return new IdentifierNode('test'); }, null],
    'Empty Array'=>[function() { return new ArrayNode(null); }, 'array'],
    'Array with one element'=>[function() { return new ArrayNode(new IntegerNode(10)); }, 'array'],
    'Array with two elements'=>[function()
    {
        $return = new ArrayNode(new IntegerNode(10));
        $return->addElement(new IntegerNode(20));
        return $return;
    }, 'array'],
    'Array with three elements'=>[function()
    {
        $return = new ArrayNode(new IntegerNode(10));
        $return->addElement(new IntegerNode(20));
        $return->addElement(new IntegerNode(30));
        return $return;
    }, 'array'],
    'Function with type set'=>[function()
    {   // Just returns if the FunctionNode asks the FunctionDescriptor for the return type
        $descriptor = \Mockery::mock(FunctionDescriptor::class);
        $descriptor->shouldReceive('getReturnType')->andReturn('integer');
        $return = new FunctionNode('test');
        $return->setFunctionDescriptor($descriptor);
        return $return;
    },'integer'],
    'Function with type not set'=>[function()
    {
        return new FunctionNode('test');
    },null],
    ]);

test('getValue() for constant nodes only', function($modifier, $expect)
{
    expect($modifier()->getValue())->toBe($expect);
})->with(
[
    'BooleanNode (true)'=>[function() { return new BooleanNode(true); }, true],
    'BooleanNode (false)'=>[function() { return new BooleanNode(false); }, false],
    'IntegerNode'=>[function() { return new IntegerNode(10); }, 10],
    'FloatNode'=>[function() { return new FloatNode(1.23); }, 1.23],
    'StringNode'=>[function() { return new StringNode('abc'); }, 'abc'],
    'DateNode'=>[function() { return new DateNode('2025-07-02'); }, '2025-07-02'],
    'DateTimeNode'=>[function() { return new DateTimeNode('2025-07-02 11:12:13'); }, '2025-07-02 11:12:13'],
    'TimeNode'=>[function() { return new TimeNode('11:12:13'); }, '11:12:13'],
]);

test('toString()', function($modifier, $expect)
{
    expect($modifier()->toString())->toBe($expect);
})->with(
[
    'BooleanNode (true)'=>[function() { return new BooleanNode(true); }, 'true'],
    'BooleanNode (false)'=>[function() { return new BooleanNode(false); }, 'false'],
    'IntegerNode'=>[function() { return new IntegerNode(10); }, '10'],
    'FloatNode'=>[function() { return new FloatNode(1.23); }, '1.23'],
    'StringNode'=>[function() { return new StringNode('abc'); }, '"abc"'],
    'DateNode'=>[function() { return new DateNode('2025-07-02'); }, '"2025-07-02"'],
    'DateTimeNode'=>[function() { return new DateTimeNode('2025-07-02 11:12:13'); }, '"2025-07-02 11:12:13"'],
    'TimeNode'=>[function() { return new TimeNode('11:12:13'); }, '"11:12:13"'],
    'IdentifierNode (type set)'=>[function()
    {
        $return = new IdentifierNode('test');
        $return->setDatatype('integer');
        return  $return;
    }, 'test'],
    'IdentifierNode (type not set)'=>[function() { return new IdentifierNode('test'); }, 'test'],
    'Empty Array'=>[function() { return new ArrayNode(null); }, '[]'],
    'Array with one element'=>[function() { return new ArrayNode(new IntegerNode(10)); }, '[10]'],
    'Array with two elements'=>[function()
    {
        $return = new ArrayNode(new IntegerNode(10));
        $return->addElement(new StringNode('abc'));
        return $return;
    }, '[10,"abc"]'],
    'Array with three elements'=>[function()
    {
        $return = new ArrayNode(new IntegerNode(10));
        $return->addElement(new IntegerNode(20));
        $return->addElement(new IntegerNode(30));
        return $return;
    }, '[10,20,30]'],
    'FunctionNode without arguments'=>[function()
    {
        return new FunctionNode('test');        
    },'test()'],
    'FunctionNode with one argument'=>[function()
    {
        $test = new FunctionNode('test');
        $test->arguments(new IntegerNode(10));
        return $test;
        
    },'test(10)'],
    'FunctionNode with two arguments'=>[function()
    {
        $arguments = new ArrayNode(new IntegerNode(10));
        $arguments->addElement(new StringNode('abc'));
        
        $test = new FunctionNode('testfunc');
        $test->arguments($arguments);
        return $test;
    },'testfunc(10,"abc")'],
    ]);

test('validate()', function($modifier, $expect)
{
    $result = true;
    try {
        $modifier()->validate();
    } catch (AnalyzerException $e) {
        $result = false;
    }
    expect($result)->toBe($expect);
})->with(
[
    'BooleanNode'=>[function() { return new BooleanNode(true); }, true],
    'IntegerNode'=>[function() { return new IntegerNode(10); }, true],
    'FloatNode'=>[function() { return new FloatNode(1.23); }, true],
    'StringNode'=>[function() { return new StringNode('abc'); }, true],
    'DateNode'=>[function() { return new DateNode('2025-07-02'); }, true],
    'DateTimeNode'=>[function() { return new DateTimeNode('2025-07-02 11:12:13'); }, true],
    'TimeNode'=>[function() { return new TimeNode('11:12:13'); }, true],
    'IdentifierNode (type set)'=>[function()
    {
        $return = new IdentifierNode('test');
        $return->setDatatype('integer');
        return  $return;
    }, true],
    'IdentifierNode (type not set)'=>[function() { return new IdentifierNode('test'); }, false],
    'Array with two elements (both valid)'=>[function()
    {
        $id1 = new IdentifierNode('id1');
        $id1->setDatatype('integer');
        $id2 = new IdentifierNode('id2');
        $id2->setDatatype('integer');
        $return = new ArrayNode($id1);
        $return->addElement($id2);
        return $return;
    }, true],
    'Array with two elements (one invalid)'=>[function()
    {
        $id1 = new IdentifierNode('id1');
        $id1->setDatatype('integer');
        $id2 = new IdentifierNode('id2');
        $return = new ArrayNode($id1);
        $return->addElement($id2);
        return $return;
    }, false],
    'Unknown Function'=>[function()
    {
       return new FunctionNode('test');   
    }, false],
    'Function without arguments passes'=>[function()
    {
        $descriptor = \Mockery::mock(FunctionDescriptor::class);
        $descriptor->shouldReceive('reset');
        $descriptor->shouldReceive('pop')->andReturn(null);
        $funct = new FunctionNode('test');
        $funct->setFunctionDescriptor($descriptor);
        
        return $funct;
    }, true],
    'Function with one argument passes'=>[function()
    {
        $descriptor = \Mockery::mock(FunctionDescriptor::class);
        $descriptor->shouldReceive('reset');
        $descriptor->shouldReceive('pop')->andReturn('!integer',null);
        $funct = new FunctionNode('test');
        $funct->arguments(new IntegerNode(10));
        $funct->setFunctionDescriptor($descriptor);
        
        return $funct;
    }, true],
    'Function with one argument fails due type mismatch'=>[function()
    {
        $descriptor = \Mockery::mock(FunctionDescriptor::class);
        $descriptor->shouldReceive('reset');
        $descriptor->shouldReceive('pop')->andReturn('!string',null);
        $funct = new FunctionNode('test');
        $funct->arguments(new IntegerNode(10));
        $funct->setFunctionDescriptor($descriptor);
        
        return $funct;
    }, false],
    'Function with one argument fails due missing argument'=>[function()
    {
        $descriptor = \Mockery::mock(FunctionDescriptor::class);
        $descriptor->shouldReceive('reset');
        $descriptor->shouldReceive('pop')->andReturn('!integer',null);
        $funct = new FunctionNode('test');
        $funct->setFunctionDescriptor($descriptor);
        
        return $funct;
    }, false],
    'Function with one mandatory and one optional argument passes (both given)'=>[function()
    {
        $arguments = new ArrayNode(new IntegerNode(10));
        $arguments->addElement(new StringNode('abc'));
        
        
        $descriptor = \Mockery::mock(FunctionDescriptor::class);
        $descriptor->shouldReceive('reset');
        $descriptor->shouldReceive('pop')->andReturn('!integer','?string');
        $funct = new FunctionNode('test');
        $funct->setFunctionDescriptor($descriptor);
        $funct->arguments($arguments);
        
        return $funct;
    }, true],
    'Function with one mandatory and one optional argument passes (optional omitted)'=>[function()
    {
        $descriptor = \Mockery::mock(FunctionDescriptor::class);
        $descriptor->shouldReceive('reset');
        $descriptor->shouldReceive('pop')->andReturn('!integer','?string');
        $funct = new FunctionNode('test');
        $funct->setFunctionDescriptor($descriptor);
        $funct->arguments(new IntegerNode(10));
        
        return $funct;
    }, true],
    'Function with two mandatory fails (one omitted)'=>[function()
    {
        $descriptor = \Mockery::mock(FunctionDescriptor::class);
        $descriptor->shouldReceive('reset');
        $descriptor->shouldReceive('pop')->andReturn('!integer','!string');
        $funct = new FunctionNode('test');
        $funct->setFunctionDescriptor($descriptor);
        $funct->arguments(new IntegerNode(10));
        
        return $funct;
    }, false],
    
    ]);

test('elementCount() for arrays', function($modifier, $expect)
{
    expect($modifier()->elementCount())->toBe($expect);    
})->with(
[
    'Empty Array'=>[function() { return new ArrayNode(null); }, 0],
    'Array with one element'=>[function() { return new ArrayNode(new IntegerNode(10)); }, 1],
    'Array with two elements'=>[function()
    {
        $return = new ArrayNode(new IntegerNode(10));
        $return->addElement(new IntegerNode(20));
        return $return;
    }, 2],
    'Array with three elements'=>[function()
    {
        $return = new ArrayNode(new IntegerNode(10));
        $return->addElement(new IntegerNode(20));
        $return->addElement(new IntegerNode(30));
        return $return;
    }, 3],
]);

test('getDataSubtype() for arrays', function($modifier, $expect)
{
    expect($modifier()->getDataSubtype())->toBe($expect);
})->with(
    [
        'Empty Array'=>[function() { return new ArrayNode(null); }, 'empty'],
        'Array with one element'=>[function() { return new ArrayNode(new IntegerNode(10)); }, 'integer'],
        'Array with two elements (two ints)'=>[function()
        {
            $return = new ArrayNode(new IntegerNode(10));
            $return->addElement(new IntegerNode(20));
            return $return;
        }, 'integer'],
        'Array with two elements (two ints)'=>[function()
        {
            $return = new ArrayNode(new FloatNode(1.23));
            $return->addElement(new FloatNode(2.34));
            return $return;
        }, 'float'],
        'Array with two elements (two strings)'=>[function()
        {
            $return = new ArrayNode(new StringNode('abc'));
            $return->addElement(new StringNode('def'));
            return $return;
        }, 'string'],
        'Array with two elements (two booleans)'=>[function()
        {
            $return = new ArrayNode(new BooleanNode(true));
            $return->addElement(new BooleanNode(false));
            return $return;
        }, 'boolean'],
        'Array with two elements (two dates)'=>[function()
        {
            $return = new ArrayNode(new DateNode('2025-07-01'));
            $return->addElement(new DateNode('2025-07-02'));
            return $return;
        }, 'date'],
        'Array with two elements (two datetimes)'=>[function()
        {
            $return = new ArrayNode(new DateTimeNode('2025-07-01 11:12:13'));
            $return->addElement(new DateTimeNode('2025-07-02 11:12:13'));
            return $return;
        }, 'datetime'],
        'Array with two elements (two times)'=>[function()
        {
            $return = new ArrayNode(new TimeNode('11:12:13'));
            $return->addElement(new TimeNode('11:12:13'));
            return $return;
        }, 'time'],
        'Array with two elements (int and float)'=>[function()
        {
            $return = new ArrayNode(new IntegerNode(10));
            $return->addElement(new FloatNode(2.34));
            return $return;
        }, 'float'],
        'Array with two elements (date and datetimes)'=>[function()
        {
            $return = new ArrayNode(new DateNode('2025-07-01'));
            $return->addElement(new DateTimeNode('2025-07-02 11:12:13'));
            return $return;
        }, 'datetime'],
        'Array with two elements (int and string)'=>[function()
        {
            $return = new ArrayNode(new IntegerNode(10));
            $return->addElement(new StringNode('abc'));
            return $return;
        }, 'mixed'],
        'Array with two elements (int and bool)'=>[function()
        {
            $return = new ArrayNode(new IntegerNode(10));
            $return->addElement(new BooleanNode(true));
            return $return;
        }, 'mixed'],
        'Array with two elements (int and date)'=>[function()
        {
            $return = new ArrayNode(new IntegerNode(10));
            $return->addElement(new DateNode('2025-07-02'));
            return $return;
        }, 'mixed'],
        'Array with two elements (int and datetime)'=>[function()
        {
            $return = new ArrayNode(new IntegerNode(10));
            $return->addElement(new DateTimeNode('2025-07-02 11:12:13'));
            return $return;
        }, 'mixed'],
        'Array with two elements (int and time)'=>[function()
        {
            $return = new ArrayNode(new IntegerNode(10));
            $return->addElement(new TimeNode('11:12:13'));
            return $return;
        }, 'mixed'],
        'Array with two elements (int and unset function)'=>[function()
        {
            $return = new ArrayNode(new IntegerNode(10));
            $return->addElement(new FunctionNode('test'));
            return $return;
        }, null],
        'Array with two elements (int and set function)'=>[function()
        {
            $descriptor = \Mockery::mock(FunctionDescriptor::class);
            $descriptor->shouldReceive('getReturnType')->andReturn('integer');
            $return = new ArrayNode(new IntegerNode(10));
            $funct = new FunctionNode('test');
            $funct->setFunctionDescriptor($descriptor);
            $return->addElement($funct);
            return $return;
        }, 'integer'],
        'Array with two elements (int and unset identifier)'=>[function()
        {
            $return = new ArrayNode(new IntegerNode(10));
            $return->addElement(new IdentifierNode('test'));
            return $return;
        }, null],
        'Array with two elements (int and set identifier)'=>[function()
        {
            $return = new ArrayNode(new IntegerNode(10));
            $funct = new IdentifierNode('test');
            $funct->setDatatype('integer');
            $return->addElement($funct);
            return $return;
        }, 'integer'],
        'Array with three elements (all int)'=>[function()
        {
            $return = new ArrayNode(new IntegerNode(10));
            $return->addElement(new IntegerNode(20));
            $return->addElement(new IntegerNode(30));
            return $return;
        }, 'integer'],
        'Array with three elements (all int but last one is a float)'=>[function()
        {
            $return = new ArrayNode(new IntegerNode(10));
            $return->addElement(new IntegerNode(20));
            $return->addElement(new FloatNode(1.23));
            return $return;
        }, 'float'],
        'Array with three elements (all int but first one is a float)'=>[function()
        {
            $return = new ArrayNode(new FloatNode(1.23));
            $return->addElement(new IntegerNode(20));
            $return->addElement(new IntegerNode(30));
            return $return;
        }, 'float'],
        'Array with three elements (all int but last one is a string)'=>[function()
        {
            $return = new ArrayNode(new IntegerNode(10));
            $return->addElement(new IntegerNode(20));
            $return->addElement(new StringNode('abc'));
            return $return;
        }, 'mixed'],
        'Array with three elements (all int but first one is a string)'=>[function()
        {
            $return = new ArrayNode(new StringNode('abc'));
            $return->addElement(new IntegerNode(20));
            $return->addElement(new IntegerNode(30));
            return $return;
        }, 'mixed'],
        ]);

test('getElement() for array with one element', function()
{
    $test = new ArrayNode(new IntegerNode(10));
    
    expect($test->getElement(0)->getValue())->toBe(10);
});

test('getElement() for array with two elements', function()
{
    $test = new ArrayNode(new IntegerNode(10));
    
    $test->addElement(new IntegerNode(20));
    expect($test->getElement(0)->getValue())->toBe(10);
    expect($test->getElement(1)->getValue())->toBe(20);
});

test('UnaryNode', function()
{
    $test = new UnaryNode('+');
    $test->child(new IntegerNode(10));
    
    expect($test->getType())->toBe('+');
    expect($test->child()->getValue())->toBe(10);
    expect($test->getDatatype())->toBe('integer');
});

test('BinaryNode', function()
{
    $test = new BinaryNode('+');
    $test->left(new IntegerNode(10));
    $test->right(new IntegerNode(20));
    
    expect($test->getType())->toBe('+');
    expect($test->left()->getValue())->toBe(10);
    expect($test->right()->getValue())->toBe(20);
});

test('getArgumentCount() for FunctionNode', function($modifier, $expect)
{
    expect($modifier()->getArgumentCount())->toBe($expect);
})->with([
    'function with no arguments'=>[function() { return new FunctionNode('testfunct'); }, 0],
    'function with one argument'=>[function() 
    { 
        $result = new FunctionNode('testfunct');
        $result->arguments(new IntegerNode(10));
        return $result; 
    }, 1],
    'function with array node as arguments'=>[function()
    {
        $arguments = new ArrayNode(new IntegerNode(10));
        $arguments->addElement(new StringNode('abc'));
        
        $test = new FunctionNode('testfunc');
        $test->arguments($arguments);
        return $test;        
    },2],
    ]);

test('FunctionNode with no arguments', function()
{
    $test = new FunctionNode('testfunc');
    expect($test->name())->toBe('testfunc');
});

test('FunctionNode with single node', function()
{
    $test = new FunctionNode('testfunc');
    $test->arguments(new IntegerNode(10));
    expect($test->getArgument(0)->getValue())->toBe(10);
});

