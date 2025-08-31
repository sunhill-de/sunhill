<?php
/**
 * @file ArrayNodeTest.php
 * tests: /src/Parser/Nodes/ArrayNode.php
 * free of dependent units: yes
 */

use Sunhill\Parser\Nodes\ArrayNode;
use Sunhill\Parser\Nodes\Node;
use Sunhill\Tests\SunhillSimpleTestCase;

uses(SunhillSimpleTestCase::class);

test('getDatatype()', function($modifier, $expect)
{
    expect($modifier()->getDatatype())->toBe($expect);
})->with([
    'Array with no element'=>[function()
    {
        return new ArrayNode();        
    },'array'
    ],
    'Array with one element'=>[function() 
    { 
        $node = \Mockery::mock(Node::class);
        return new ArrayNode($node); 
    }, 'array'],
    'Array with two elements'=>[function()
    {
        $node1 = \Mockery::mock(Node::class);
        $node2 = \Mockery::mock(Node::class);        
        $return = new ArrayNode($node1);
        $return->addElement($node2);
        return $return;
    }, 'array'],
    'Array with three elements'=>[function()
    {
        $node1 = \Mockery::mock(Node::class);
        $node2 = \Mockery::mock(Node::class);
        $node3 = \Mockery::mock(Node::class);
        $return = new ArrayNode($node1);
        $return->addElement($node2);
        $return->addElement($node3);
        return $return;
    }, 'array'],    
]);

test('toString()', function($modifier, $expect)
{
    expect($modifier()->toString())->toBe($expect);
})->with([
    'Array with no element'=>[function()
    {
        return new ArrayNode();
    },'[]'],
    'Array with one element'=>[function()
    {
            $node = \Mockery::mock(Node::class);
            $node->shouldReceive('toString')->andReturn(10);
            return new ArrayNode($node);
    }, '[10]'],
    'Array with two elements'=>[function()
    {
        $node1 = \Mockery::mock(Node::class);
        $node1->shouldReceive('toString')->andReturn(10);
        $node2 = \Mockery::mock(Node::class);
        $node2->shouldReceive('toString')->andReturn(20);
        $return = new ArrayNode($node1);
        $return->addElement($node2);
        return $return;
    }, '[10,20]'],
    'Array with three elements'=>[function()
    {
        $node1 = \Mockery::mock(Node::class);
        $node1->shouldReceive('toString')->andReturn(10);
        $node2 = \Mockery::mock(Node::class);
        $node2->shouldReceive('toString')->andReturn(20);
        $node3 = \Mockery::mock(Node::class);
        $node3->shouldReceive('toString')->andReturn(30);
        $return = new ArrayNode($node1);
        $return->addElement($node2);
        $return->addElement($node3);
        return $return;
    }, '[10,20,30]'],
 ]);

test('validate()', function($modifier)
{
    $test = $modifier;
    $test->validate();
    expect(true)->toBe(true);
})->with([
    'Array with no element'=>[function()
    {
        return new ArrayNode();
    }],
    'Array with one element'=>[function()
    {
        $node = \Mockery::mock(Node::class);
        $node->shouldReceive('validate')->once();
        return new ArrayNode($node);
    }],
    'Array with two elements'=>[function()
    {
        $node1 = \Mockery::mock(Node::class);
        $node1->shouldReceive('validate')->once();
        $node2 = \Mockery::mock(Node::class);
        $node2->shouldReceive('validate')->once();
        $return = new ArrayNode($node1);
        $return->addElement($node2);
        return $return;
    }],
    'Array with three elements'=>[function()
    {
        $node1 = \Mockery::mock(Node::class);
        $node1->shouldReceive('validate')->once();
        $node2 = \Mockery::mock(Node::class);
        $node2->shouldReceive('validate')->once();
        $node3 = \Mockery::mock(Node::class);
        $node3->shouldReceive('validate')->once();
        $return = new ArrayNode($node1);
        $return->addElement($node2);
        $return->addElement($node3);
        return $return;
    }],
]);

test('elementCount() for arrays', function($modifier, $expect)
{
    expect($modifier()->elementCount())->toBe($expect);
})->with([
    'Array with no element'=>[function()
    {
        return new ArrayNode();
    },0],
    'Array with one element'=>[function()
    {
        $node = \Mockery::mock(Node::class);
        return new ArrayNode($node);
    },1],
    'Array with two elements'=>[function()
    {
        $node1 = \Mockery::mock(Node::class);
        $node2 = \Mockery::mock(Node::class);
        $return = new ArrayNode($node1);
        $return->addElement($node2);
        return $return;
    },2],
    'Array with three elements'=>[function()
    {
        $node1 = \Mockery::mock(Node::class);
        $node2 = \Mockery::mock(Node::class);
        $node3 = \Mockery::mock(Node::class);
        $return = new ArrayNode($node1);
        $return->addElement($node2);
        $return->addElement($node3);
        return $return;
    },3],
]);

test('getDataSubtype() for empty array', function()
{
    $test = new ArrayNode();
    expect($test->getDataSubtype())->toBe('empty');
});

test('getDataSubtype() for array with one', function()
{
    $first_node = \Mockery::mock(Node::class);
    $first_node->shouldReceive('getDatatype')->once()->andReturn('integer');
    $test = new ArrayNode($first_node);
    expect($test->getDataSubtype())->toBe('integer');
});

test('getDataSubtype() for arrays with two elements', function($first, $second, $expect)
{
    $first_node = \Mockery::mock(Node::class);
    $first_node->shouldReceive('getDatatype')->once()->andReturn($first);
    $second_node = \Mockery::mock(Node::class);
    $second_node->shouldReceive('getDatatype')->once()->andReturn($second);
    $test = new ArrayNode($first_node);
    $test->addElement($second_node);
    expect($test->getDataSubtype())->toBe($expect);
})->with(
    [
        '[int,int]'=>['integer','integer','integer'],
        '[float,float]'=>['float','float','float'],
        '[string,string]'=>['string','string','string'],        
        '[boolean,boolean]'=>['boolean','boolean','boolean'],
        '[date,date]'=>['date','date','date'],
        '[datetime,datetime]'=>['datetime','datetime','datetime'],
        '[time,time]'=>['time','time','time'],
        '[int,float]'=>['integer','float','float'],
        '[date,datetime]'=>['date','datetime','datetime'],
        '[int,string]'=>['integer','string','mixed'],
        '[int,boolean]'=>['integer','boolean','mixed'],
        '[int,date]'=>['integer','date','mixed'],
        '[int,datetime]'=>['integer','datetime','mixed'],
        '[int,time]'=>['integer','time','mixed'],
        '[int,null]'=>['integer',null,null],
]);

test('getDataSubtype() for arrays with three elements', function($first, $second, $third, $expect)
{
    $first_node = \Mockery::mock(Node::class);
    $first_node->shouldReceive('getDatatype')->once()->andReturn($first);
    $second_node = \Mockery::mock(Node::class);
    $second_node->shouldReceive('getDatatype')->once()->andReturn($second);
    $third_node = \Mockery::mock(Node::class);
    $third_node->shouldReceive('getDatatype')->once()->andReturn($third);
    $test = new ArrayNode($first_node);
    $test->addElement($second_node);
    $test->addElement($third_node);
    expect($test->getDataSubtype())->toBe($expect);
})->with(
[
    '[int,int,int]'=>['integer','integer','integer','integer'],
    '[int,int,float]'=>['integer','integer','float','float'],
    '[float,int,int]'=>['float','integer','integer','float'],
    '[int,int,string]'=>['integer','integer','string','mixed'],
    '[string,int,int]'=>['string','integer','integer','mixed'],
]);

test('getElement() for array with one element', function()
{
    $node = \Mockery::mock(Node::class);
    $test = new ArrayNode($node);
    
    expect($test->getElement(0))->toBe($node);
});

test('getElement() for array with two elements', function()
{
    $node1 = \Mockery::mock(Node::class);
    $node2 = \Mockery::mock(Node::class);
    $test = new ArrayNode($node1);
    $test->addElement($node2);
    
    expect($test->getElement(0))->toBe($node1);
    expect($test->getElement(1))->toBe($node2);
});

