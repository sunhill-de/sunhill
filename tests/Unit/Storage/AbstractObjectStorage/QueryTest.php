<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Query\QueryParser\QueryNode;
use Sunhill\Tests\Unit\Storage\AbstractObjectStorage\DummyAbstractObjectStorage;
use Sunhill\Facades\Properties;
use Sunhill\Parser\Nodes\BinaryNode;
use Sunhill\Parser\Nodes\IdentifierNode;
use Sunhill\Parser\Nodes\IntegerNode;

uses(SunhillTestCase::class);

function getStorageForQuery()
{
    Properties::shouldReceive('loadAttribute')->with(1,1)->andReturn(makeStdclass(['name'=>'attribute1','type'=>'string','value'=>'value1']));
    Properties::shouldReceive('loadAttribute')->with(2,1)->andReturn(makeStdclass(['name'=>'attribute2','type'=>'string','value'=>'value2']));    
    Properties::shouldReceive('loadAttribute')->with(2,2)->andReturn(makeStdclass(['name'=>'attribute2','type'=>'string','value'=>'value2']));
    Properties::shouldReceive('loadAttribute')->with(3,2)->andReturn(makeStdclass(['name'=>'attribute3','type'=>'string','value'=>'value4']));
    Properties::shouldReceive('loadAttribute')->with(4,3)->andReturn(makeStdclass(['name'=>'attribute4','type'=>'string','value'=>'value4']));
    

    $test = new DummyAbstractObjectStorage();
    $test::$DataPool = $test::$Data;  
    return $test;
}


test('Execute query for select without anything else', function()
{
    $test = getStorageForQuery();
    $query = new QueryNode();
    $query->verb('select');
    
    $result = $test->executeQuery($query);
    
    expect(count($result))->toBe(4);
});

test('Execute query for select with a where statement', function()
{
    $test = getStorageForQuery();
    $query = new QueryNode();
    $query->verb('select');
    $where = new BinaryNode('<');
    $where->left(new IdentifierNode('child_int'));
    $where->right(new IntegerNode(333));
    $query->setWhere($where);
    
    $result = $test->executeQuery($query);
    
    expect(count($result))->toBe(2);
});