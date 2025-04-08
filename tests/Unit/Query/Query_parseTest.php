<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Query\Query;
use Sunhill\Parser\Nodes\IntegerNode;
use Sunhill\Tests\Unit\Parser\Examples\DummyExecutor;
use Sunhill\Facades\Queries;
use Sunhill\Parser\Nodes\StringNode;
use Sunhill\Parser\Nodes\BinaryNode;
use Sunhill\Parser\Nodes\IdentifierNode;
use Sunhill\Query\QueryParser\OrderNode;
use Sunhill\Query\Exceptions\InvalidOrderException;
use Sunhill\Parser\Nodes\ArrayNode;
use Sunhill\Parser\Nodes\FunctionNode;

uses(SunhillTestCase::class);

test('Empty query', function()
{
    $test = new Query();
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))
        ->toBe('select,fields:[],where:[],order:[],group:[],offset:[],limit:[]');    
});

test('Offset signatures', function($input, $expect)
{
    $node = new BinaryNode('+');
    $node->left(new IntegerNode(5));
    $node->right(new IntegerNode(3));
    Queries::shouldReceive('parseQueryString')->with('5+3')->andReturn($node);
    Queries::shouldReceive('parseQueryString')->with("5")->andReturn(new IntegerNode(5));
    Queries::shouldReceive('parseQueryString')->with("'5'")->andReturn(new StringNode("5"));
    
    $test = new Query();
    $test->offset($input);
    
    $executor = new DummyExecutor();
    $expect = str_replace('*',$expect,'select,fields:[],where:[],order:[],group:[],offset:[*],limit:[]');
    
    expect($executor->execute($test->getQueryNode()))->toBe($expect);
})->with(
    [
        'simple integer'=>[5,'5'],
        'callback'=>[function() { return 5; },'5'],
        'expression'=>['5+3', '(5)+(3)'],
        'integer expression'=>["5", '5'] ,       
        'string'=>["'5'", '"5"']
        ]);

test('Offset: A node', function()
{
    $test = new Query();
    $test->offset(new IntegerNode(5));
    
    $ast = $test->getQueryNode();
    expect($ast->offset()->getType())->toBe('integer');
    expect($ast->offset()->getValue())->toBe(5);
});

// ================================ limit ========================================
test('Limit signatures', function($input, $expect)
{
    $node = new BinaryNode('+');
    $node->left(new IntegerNode(5));
    $node->right(new IntegerNode(3));
    Queries::shouldReceive('parseQueryString')->with('5+3')->andReturn($node);
    Queries::shouldReceive('parseQueryString')->with("5")->andReturn(new IntegerNode(5));
    Queries::shouldReceive('parseQueryString')->with("'5'")->andReturn(new StringNode("5"));
    
    $test = new Query();
    $test->limit($input);
    
    $executor = new DummyExecutor();
    $expect = str_replace('*',$expect,'select,fields:[],where:[],order:[],group:[],offset:[],limit:[*]');
    
    expect($executor->execute($test->getQueryNode()))->toBe($expect);
})->with(
    [
        'simple integer'=>[5,'5'],
        'callback'=>[function() { return 5; },'5'],
        'expression'=>['5+3', '(5)+(3)'],
        'integer expression'=>["5", '5'] ,
        'string'=>["'5'", '"5"']
        ]);

test('Limit: A node', function()
{
    $test = new Query();
    $test->offset(new IntegerNode(5));
    
    $ast = $test->getQueryNode();
    expect($ast->offset()->getType())->toBe('integer');
    expect($ast->offset()->getValue())->toBe(5);
});

// ============================ Order ===================================
test('Order: Just two strings', function()
{
    Queries::shouldReceive('parseQueryString')->with("a")->once()->andReturn(new IdentifierNode('a'));
    
    $test = new Query();
    $test->order("a","ASC");
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))
        ->toBe('select,fields:[],where:[],order:[a asc],group:[],offset:[],limit:[]');    
});

test('Order: Just a string (direction omitted)', function()
{
    Queries::shouldReceive('parseQueryString')->with("a")->once()->andReturn(new IdentifierNode('a'));
    
    $test = new Query();
    $test->order("a");
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))
    ->toBe('select,fields:[],where:[],order:[a asc],group:[],offset:[],limit:[]');
});

test('Order: Just a string with order statement', function()
{
    $result = new OrderNode();
    $result->field(new IdentifierNode('a'));
    $result->direction('desc');
    Queries::shouldReceive('parseQueryString')->with("a desc")->once()->andReturn($result);
    
    $test = new Query();
    $test->order("a desc");
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))
    ->toBe('select,fields:[],where:[],order:[a desc],group:[],offset:[],limit:[]');
});

test('Order: stdclass with direction', function()
{
    Queries::shouldReceive('parseQueryString')->with("a")->once()->andReturn(new IdentifierNode('a'));
    
    $test = new Query();
    $return = new \stdClass(); 
    $return->field = 'a'; 
    $return->direction = 'desc';
    $test->order($return);
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))
    ->toBe('select,fields:[],where:[],order:[a desc],group:[],offset:[],limit:[]');
});

test('Order: stdclass without direction', function()
{
    Queries::shouldReceive('parseQueryString')->with("a")->once()->andReturn(new IdentifierNode('a'));
    
    $test = new Query();
    $return = new \stdClass(); 
    $return->field = 'a';
    $test->order($return);
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))
    ->toBe('select,fields:[],where:[],order:[a asc],group:[],offset:[],limit:[]');
});

test('Order: Callback returning a string with direction', function()
{
    $result = new OrderNode();
    $result->field(new IdentifierNode('a'));
    $result->direction('desc');
    Queries::shouldReceive('parseQueryString')->with("a desc")->once()->andReturn($result);
    
    $test = new Query();
    $test->order(function() { return "a desc"; });
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))
    ->toBe('select,fields:[],where:[],order:[a desc],group:[],offset:[],limit:[]');
});

test('Order: callback returning a string without direction', function()
{
    Queries::shouldReceive('parseQueryString')->with("a")->once()->andReturn(new IdentifierNode('a'));
    
    $test = new Query();
    $test->order(function() { return "a"; });
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))
    ->toBe('select,fields:[],where:[],order:[a asc],group:[],offset:[],limit:[]');
});

test('Order: callback returning a stdclass with direction', function()
{
    Queries::shouldReceive('parseQueryString')->with("a")->once()->andReturn(new IdentifierNode('a'));
    
    $test = new Query();
    $test->order(function() 
    { 
        $return = new \stdClass(); 
        $return->field = 'a'; 
        $return->direction = 'desc';
        
        return $return;
    });
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))
    ->toBe('select,fields:[],where:[],order:[a desc],group:[],offset:[],limit:[]');
});

test('Order: callback returning a stdclass without direction', function()
{
    Queries::shouldReceive('parseQueryString')->with("a")->once()->andReturn(new IdentifierNode('a'));
    
    $test = new Query();
    $test->order(function() 
    { 
        $return = new \stdClass(); 
        $return->field = 'a';
        
        return $return;
    });
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))
    ->toBe('select,fields:[],where:[],order:[a asc],group:[],offset:[],limit:[]');
});

test('Order: it fails when invalid direction is given', function()
{
    Queries::shouldReceive('parseQueryString')->with("a")->once()->andReturn(new IdentifierNode('a'));
    
    $test = new Query();
    $test->order('a','invalid');    
})->throws(InvalidOrderException::class);


// ================================ fields ===================================
test('Fields: single field', function()
{
    Queries::shouldReceive('parseQueryString')->with('a')->once()->andReturn(new IdentifierNode('a'));
    
    $test = new Query();
    $test->fields('a');
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))->toBe('select,fields:[a],where:[],order:[],group:[],offset:[],limit:[]');
});

test('Fields: qualified single field', function()
{
    $return = new IdentifierNode('a');
    $return->reference(new IdentifierNode('sample'));
    Queries::shouldReceive('parseQueryString')->with('sample.a')->once()->andReturn($return);
    
    $test = new Query();
    $test->fields('sample.a');
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))->toBe('select,fields:[{sample}.a],where:[],order:[],group:[],offset:[],limit:[]');
});

test('Fields: referenced single field', function()
{
    $return = new IdentifierNode('b');
    $return->parent(new IdentifierNode('a'));
    Queries::shouldReceive('parseQueryString')->with('a->b')->once()->andReturn($return);
    
    $test = new Query();
    $test->fields('a->b');
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))->toBe('select,fields:[{a}->b],where:[],order:[],group:[],offset:[],limit:[]');
});

test('Fields: function as single field', function()
{
    $return = new FunctionNode('sin');
    $return->arguments(new IdentifierNode('a'));
    Queries::shouldReceive('parseQueryString')->with('sin(a)')->once()->andReturn($return);
    
    $test = new Query();
    $test->fields('sin(a)');
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))->toBe('select,fields:[sin({a})],where:[],order:[],group:[],offset:[],limit:[]');
});

test('Fields: multiple fields', function()
{
    $return = new ArrayNode(new IdentifierNode('a'));
    $return->addElement(new IdentifierNode('b'));
    $return->addElement(new IdentifierNode('c'));
    Queries::shouldReceive('parseQueryString')->with('a,b,c')->once()->andReturn($return);
    
    $test = new Query();
    $test->fields('a,b,c');
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))->toBe('select,fields:[[{a},{b},{c}]],where:[],order:[],group:[],offset:[],limit:[]');
});

test('Fields: multiple fields passed as array', function()
{
    Queries::shouldReceive('parseQueryString')->with('a')->once()->andReturn(new IdentifierNode('a'));
    Queries::shouldReceive('parseQueryString')->with('b')->once()->andReturn(new IdentifierNode('b'));
    Queries::shouldReceive('parseQueryString')->with('c')->once()->andReturn(new IdentifierNode('c'));
    
    $test = new Query();
    $test->fields(['a','b','c']);
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))->toBe('select,fields:[[{a},{b},{c}]],where:[],order:[],group:[],offset:[],limit:[]');
});

test('Fields: multiple fields passed as collection', function()
{
    Queries::shouldReceive('parseQueryString')->with('a')->once()->andReturn(new IdentifierNode('a'));
    Queries::shouldReceive('parseQueryString')->with('b')->once()->andReturn(new IdentifierNode('b'));
    Queries::shouldReceive('parseQueryString')->with('c')->once()->andReturn(new IdentifierNode('c'));
    
    $test = new Query();
    $test->fields(collect(['a','b','c']));
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))->toBe('select,fields:[[{a},{b},{c}]],where:[],order:[],group:[],offset:[],limit:[]');
});

// ===================================== Where ========================================================
test('Simple where signatures', function($where, $input, $expect)
{
    $expression1 = new BinaryNode('>');
    $expression1->left(new IdentifierNode('a'));
    $expression1->right(new IntegerNode(5));
    Queries::shouldReceive('parseQueryString')->with('a>5')->andReturn($expression1);
    $expression2 = new BinaryNode('+');
    $function = new FunctionNode('sin');
    $function->arguments(new IdentifierNode('a'));
    $expression2->left($function);
    $expression2->right(new IntegerNode(2));
    Queries::shouldReceive('parseQueryString')->with('sin(a)+2')->andReturn($expression2);
    Queries::shouldReceive('parseQueryString')->with('a')->andReturn(new IdentifierNode('a'));
    Queries::shouldReceive('parseQueryString')->with('abc')->andReturn(new StringNode('abc'));
    
    $test = new Query();
    $test->$where(...$input);
    
    $executor = new DummyExecutor();
    $expect = str_replace('*',$expect, 'select,fields:[],where:[*],order:[],group:[],offset:[],limit:[]');
    expect($executor->execute($test->getQueryNode()))->toBe($expect);    
})->with(
    [
        'where with 3 strings'=>['where',['a','=','abc'],'(a)=("abc")'],
        'where with 2 string and 1 integer'=>['where',['a','=',1],'(a)=(1)'],
        'where with 2 string and 1 float'=>['where',['a','=',1.23],'(a)=(1.23)'],
        'where with 2 string and 1 boolean'=>['where',['a','=',true],'(a)=(true)'],
        'where with 2 strings'=>['where',['a','abc'],'(a)=("abc")'],
        'where with 1 string and 1 integer'=>['where',['a',1],'(a)=(1)'],
        'where with 1 string and 1 float'=>['where',['a',1.23],'(a)=(1.23)'],
        'where with 1 string and 1 boolean'=>['where',['a',true],'(a)=(true)'],
        'where with 1 string (implicit boolean)'=>['where',['a'],'a'],
        'where with 1 string (expression)'=>['where',['sin(a)+2'],'(sin({a}))+(2)'],
        'where with 1 string (boolean expression)'=>['where',['a>5'],'(a)>(5)'],
        'where with 3 callbacks'=>['where',[function() { return 'a'; },function() { return '='; },function() { return 'abc'; }],'(a)=("abc")'],
        
        'orWhere with 3 strings'=>['orWhere',['a','=','abc'],'(a)=("abc")'],
        'orWhere with 2 string and 1 integer'=>['orWhere',['a','=',1],'(a)=(1)'],
        'orWhere with 2 string and 1 float'=>['orWhere',['a','=',1.23],'(a)=(1.23)'],
        'orWhere with 2 string and 1 boolean'=>['orWhere',['a','=',true],'(a)=(true)'],
        'orWhere with 2 strings'=>['orWhere',['a','abc'],'(a)=("abc")'],
        'orWhere with 1 string and 1 integer'=>['orWhere',['a',1],'(a)=(1)'],
        'orWhere with 1 string and 1 float'=>['orWhere',['a',1.23],'(a)=(1.23)'],
        'orWhere with 1 string and 1 boolean'=>['orWhere',['a',true],'(a)=(true)'],
        'orWhere with 1 string (implicit boolean)'=>['orWhere',['a'],'a'],
        'orWhere with 1 string (expression)'=>['orWhere',['sin(a)+2'],'(sin({a}))+(2)'],
        'orWhere with 1 string (boolean expression)'=>['orWhere',['a>5'],'(a)>(5)'],
        'orWhere with 3 callbacks'=>['orWhere',[function() { return 'a'; },function() { return '='; },function() { return 'abc'; }],'(a)=("abc")'],
        
        'whereNot with 3 string'=>['whereNot',['a','=','abc'],'!((a)=("abc"))'],
        'whereNot with 2 strings and 1 integer'=>['whereNot',['a','=',1],'!((a)=(1))'],
        'whereNot with 2 strings and 1 float'=>['whereNot',['a','=',1.23],'!((a)=(1.23))'],
        'whereNot with 2 strings and 1 boolean'=>['whereNot',['a','=',true],'!((a)=(true))'],
        'whereNot with 2 string'=>['whereNot',['a','abc'],'!((a)=("abc"))'],
        'whereNot with 1 strings and 1 integer'=>['whereNot',['a',1],'!((a)=(1))'],
        'whereNot with 1 strings and 1 float'=>['whereNot',['a',1.23],'!((a)=(1.23))'],
        'whereNot with 1 strings and 1 boolean'=>['whereNot',['a',true],'!((a)=(true))'],
        'whereNot with 1 string (implicit boolean)'=>['whereNot',['a'],'!(a)'],
        'whereNot with 1 string (expression)'=>['whereNot',['sin(a)+2'],'!((sin({a}))+(2))'],
        'whereNot with 1 string (boolean expression)'=>['whereNot',['a>5'],'!((a)>(5))'],
        'whereNot with 3 callbacks'=>['whereNot',[function() { return 'a'; },function() { return '='; },function() { return 'abc'; }],'!((a)=("abc"))'],
        
        'orWhereNot with 3 strings'=>['orWhereNot',['a','=','abc'],'!((a)=("abc"))'],
        'orWhereNot with 2 strings and 1 integer'=>['orWhereNot',['a','=',1],'!((a)=(1))'],
        'orWhereNot with 2 strings and 1 float'=>['orWhereNot',['a','=',1.23],'!((a)=(1.23))'],
        'orWhereNot with 2 strings and 1 boolean'=>['orWhereNot',['a','=',true],'!((a)=(true))'],
        'orWhereNot with 2 strings'=>['orWhereNot',['a','abc'],'!((a)=("abc"))'],
        'orWhereNot with 1 strings and 1 integer'=>['orWhereNot',['a',1],'!((a)=(1))'],
        'orWhereNot with 1 strings and 1 float'=>['orWhereNot',['a',1.23],'!((a)=(1.23))'],
        'orWhereNot with 1 strings and 1 boolean'=>['orWhereNot',['a',true],'!((a)=(true))'],
        'orWhereNot with 1 string (implicit boolean)'=>['orWhereNot',['a'],'!(a)'],
        'orWhereNot with 1 string (expression)'=>['orWhereNot',['sin(a)+2'],'!((sin({a}))+(2))'],
        'orWhereNot with 1 string (boolean expression)'=>['orWhereNot',['a>5'],'!((a)>(5))'],
        'orWhereNot with 3 callbacks'=>['orWhereNot',[function() { return 'a'; },function() { return '='; },function() { return 'abc'; }],'!((a)=("abc"))'],
        ]
    );

test('combined where signatures', function($where, $input, $expect)
{
    $expression1 = new BinaryNode('>');
    $expression1->left(new IdentifierNode('a'));
    $expression1->right(new IntegerNode(5));
    Queries::shouldReceive('parseQueryString')->with('a>5')->andReturn($expression1);
    $expression2 = new BinaryNode('+');
    $function = new FunctionNode('sin');
    $function->arguments(new IdentifierNode('a'));
    $expression2->left($function);
    $expression2->right(new IntegerNode(2));
    Queries::shouldReceive('parseQueryString')->with('sin(a)+2')->andReturn($expression2);
    Queries::shouldReceive('parseQueryString')->with('a')->andReturn(new IdentifierNode('a'));
    Queries::shouldReceive('parseQueryString')->with('abc')->andReturn(new StringNode('abc'));
    Queries::shouldReceive('parseQueryString')->with('b')->andReturn(new IdentifierNode('b'));
    Queries::shouldReceive('parseQueryString')->with('def')->andReturn(new StringNode('def'));
    
    $test = new Query();
    $test->where('b','=','def')->$where(...$input);
    
    $executor = new DummyExecutor();
    $expect = str_replace('*',$expect, 'select,fields:[],where:[((b)=("def"))*],order:[],group:[],offset:[],limit:[]');
    expect($executor->execute($test->getQueryNode()))->toBe($expect);    
})->with(
    [
        'where with 3 strings'=>['where',['a','=','abc'],'&&((a)=("abc"))'],
        'where with 2 string and 1 integer'=>['where',['a','=',1],'&&((a)=(1))'],
        'where with 2 string and 1 float'=>['where',['a','=',1.23],'&&((a)=(1.23))'],
        'where with 2 string and 1 boolean'=>['where',['a','=',true],'&&((a)=(true))'],
        'where with 2 string and 1 array'=>['where',['a','=',[1,2,3]],'&&((a)=([{1},{2},{3}]))'],
        'where with 2 strings'=>['where',['a','abc'],'&&((a)=("abc"))'],
        'where with 1 string and 1 integer'=>['where',['a',1],'&&((a)=(1))'],
        'where with 1 string and 1 float'=>['where',['a',1.23],'&&((a)=(1.23))'],
        'where with 1 string and 1 boolean'=>['where',['a',true],'&&((a)=(true))'],
        'where with 1 string and 1 array'=>['where',['a',[1,2,3]],'&&((a)=([{1},{2},{3}]))'],
        'where with 1 string (implicit boolean)'=>['where',['a'],'&&(a)'],
        'where with 1 string (expression)'=>['where',['sin(a)+2'],'&&((sin({a}))+(2))'],
        'where with 1 string (boolean expression)'=>['where',['a>5'],'&&((a)>(5))'],
        'where with 3 callbacks'=>['where',[function() { return 'a'; },function() { return '='; },function() { return 'abc'; }],'&&((a)=("abc"))'],
        'where with 1 callback'=>['where',[function($query) { $query->where('a','=',1)->where('b','=',2); }], '&&(((a)=(1))&&((b)=(2)))'],
        'where with 1 array'=>['where',[['a','=',1],['b','=',2]], '&&(((a)=(1))&&((b)=(2)))'],
        
        'orWhere with 3 strings'=>['orWhere',['a','=','abc'],'||((a)=("abc"))'],
        'orWhere with 2 string and 1 integer'=>['orWhere',['a','=',1],'||((a)=(1))'],
        'orWhere with 2 string and 1 float'=>['orWhere',['a','=',1.23],'||((a)=(1.23))'],
        'orWhere with 2 string and 1 boolean'=>['orWhere',['a','=',true],'||((a)=(true))'],
        'orWhere with 2 string and 1 array'=>['orWhere',['a','=',[1,2,3]],'||((a)=([{1},{2},{3}]))'],
        'orWhere with 2 strings'=>['orWhere',['a','abc'],'||((a)=("abc"))'],
        'orWhere with 1 string and 1 integer'=>['orWhere',['a',1],'||((a)=(1))'],
        'orWhere with 1 string and 1 float'=>['orWhere',['a',1.23],'||((a)=(1.23))'],
        'orWhere with 1 string and 1 boolean'=>['orWhere',['a',true],'||((a)=(true))'],
        'orWhere with 1 string and 1 array'=>['orWhere',['a',[1,2,3]],'||((a)=([{1},{2},{3}]))'],
        'orWhere with 1 string (implicit boolean)'=>['orWhere',['a'],'||(a)'],
        'orWhere with 1 string (expression)'=>['orWhere',['sin(a)+2'],'||((sin({a}))+(2))'],
        'orWhere with 1 string (boolean expression)'=>['orWhere',['a>5'],'||((a)>(5))'],
        'orWhere with 3 callbacks'=>['orWhere',[function() { return 'a'; },function() { return '='; },function() { return 'abc'; }],'||((a)=("abc"))'],
        'orWhere with 1 callback'=>['orWhere',[function($query) { $query->where('a','=',1)->where('b','=',2); }], '||(((a)=(1))&&((b)=(2)))'],
        'orWhere with 1 array'=>['orWhere',[['a','=',1],['b','=',2]], '||(((a)=(1))||((b)=(2)))'],
        
        'whereNot with 3 string'=>['whereNot',['a','=','abc'],'&&(!((a)=("abc")))'],
        'whereNot with 2 strings and 1 integer'=>['whereNot',['a','=',1],'&&(!((a)=(1)))'],
        'whereNot with 2 strings and 1 float'=>['whereNot',['a','=',1.23],'&&(!((a)=(1.23)))'],
        'whereNot with 2 strings and 1 boolean'=>['whereNot',['a','=',true],'&&(!((a)=(true)))'],
        'whereNot with 2 strings and 1 array'=>['whereNot',['a','=',[1,2,3]],'&&(!((a)=([{1},{2},{3}])))'],
        'whereNot with 2 string'=>['whereNot',['a','abc'],'&&(!((a)=("abc")))'],
        'whereNot with 1 strings and 1 integer'=>['whereNot',['a',1],'&&(!((a)=(1)))'],
        'whereNot with 1 strings and 1 float'=>['whereNot',['a',1.23],'&&(!((a)=(1.23)))'],
        'whereNot with 1 strings and 1 boolean'=>['whereNot',['a',true],'&&(!((a)=(true)))'],
        'whereNot with 1 strings and 1 array'=>['whereNot',['a',[1,2,3]],'&&(!((a)=([{1},{2},{3}])))'],
        'whereNot with 1 string (implicit boolean)'=>['whereNot',['a'],'&&(!(a))'],
        'whereNot with 1 string (expression)'=>['whereNot',['sin(a)+2'],'&&(!((sin({a}))+(2)))'],
        'whereNot with 1 string (boolean expression)'=>['whereNot',['a>5'],'&&(!((a)>(5)))'],
        'whereNot with 3 callbacks'=>['whereNot',[function() { return 'a'; },function() { return '='; },function() { return 'abc'; }],'&&(!((a)=("abc")))'],
        'whereNot with 1 callback'=>['whereNot',[function($query) { $query->where('a','=',1)->where('b','=',2); }], '&&(!(((a)=(1))&&((b)=(2))))'],
        'whereNot with 1 array'=>['whereNot',[['a','=',1],['b','=',2]], '&&((!((a)=(1)))&&(!((b)=(2))))'],
        
        'orWhereNot with 3 strings'=>['orWhereNot',['a','=','abc'],'||(!((a)=("abc")))'],
        'orWhereNot with 2 strings and 1 integer'=>['orWhereNot',['a','=',1],'||(!((a)=(1)))'],
        'orWhereNot with 2 strings and 1 float'=>['orWhereNot',['a','=',1.23],'||(!((a)=(1.23)))'],
        'orWhereNot with 2 strings and 1 boolean'=>['orWhereNot',['a','=',true],'||(!((a)=(true)))'],
        'orWhereNot with 2 strings and 1 array'=>['orWhereNot',['a','=',[1,2,3]],'||(!((a)=([{1},{2},{3}])))'],
        'orWhereNot with 2 strings'=>['orWhereNot',['a','abc'],'||(!((a)=("abc")))'],
        'orWhereNot with 1 strings and 1 integer'=>['orWhereNot',['a',1],'||(!((a)=(1)))'],
        'orWhereNot with 1 strings and 1 float'=>['orWhereNot',['a',1.23],'||(!((a)=(1.23)))'],
        'orWhereNot with 1 strings and 1 boolean'=>['orWhereNot',['a',true],'||(!((a)=(true)))'],
        'orWhereNot with 1 strings and 1 array'=>['orWhereNot',['a',[1,2,3]],'||(!((a)=([{1},{2},{3}])))'],
        'orWhereNot with 1 string (implicit boolean)'=>['orWhereNot',['a'],'||(!(a))'],
        'orWhereNot with 1 string (expression)'=>['orWhereNot',['sin(a)+2'],'||(!((sin({a}))+(2)))'],
        'orWhereNot with 1 string (boolean expression)'=>['orWhereNot',['a>5'],'||(!((a)>(5)))'],
        'orWhereNot with 3 callbacks'=>['orWhereNot',[function() { return 'a'; },function() { return '='; },function() { return 'abc'; }],'||(!((a)=("abc")))'],
        'orWhereNot with 1 callback'=>['orWhereNot',[function($query) { $query->where('a','=',1)->where('b','=',2); }], '||(!(((a)=(1))&&((b)=(2))))'],
        'orWhereNot with 1 array'=>['orWhereNot',[['a','=',1],['b','=',2]], '||((!((a)=(1)))||(!((b)=(2))))'],
        
        'whereIn with one string and one array'=>['whereIn',['a',[1,2,3]], '&&((a)in([{1},{2},{3}]))'],
        'orWhereIn with one string and one array'=>['orWhereIn',['a',[1,2,3]], '||((a)in([{1},{2},{3}]))'],
        'whereNotIn with one string and one array'=>['whereNotIn',['a',[1,2,3]], '&&(!((a)in([{1},{2},{3}])))'],
        'orWhereNotIn with one string and one array'=>['orWhereNotIn',['a',[1,2,3]], '||(!((a)in([{1},{2},{3}])))'],,
        
        'whereLike with one string and one string'=>['whereLike',['a','abc%'], '&&((a)like("abc%"))'],
        'orWhereLike with one string and one string'=>['orWhereLike',['a','abc%'], '||((a)like("abc%))'],
        'whereNotLike with one string and one string'=>['whereNotLike',['a',"'abc%'"], '&&(!((a)like("abc%"])))'],
        'orWhereNotLike with one string and one string'=>['orWhereNotLike',['a','abc%'], '||(!((a)like(["abc%"])))'],,
        
        'whereBetween with one string and one array'=>['whereBetween',['a',[1,2]], '&&(((a)>(1)&&(a)<(2))'],
        'orWhereBetween with one stringg and one array'=>['orWhereBetween',['a',[1,2]], '||(((a)>(1)&&(a)<(2))'],
        'whereNotBetween with one stringg and one array'=>['whereNotBetween',['a',[1,2]], '&&(!(((a)>(1)&&(a)<(2)))'],
        'orWhereNotBetween with one strinng and one array'=>['orWhereNotBetween',['a',[1,2]], '||(!(((a)>(1)&&(a)<(2)))'],,
        
        'whereAny with array and two string'=>['whereAny',[['a','b'],'=',1],'&&(((a)=(1))||((b)=(2)))'],
        'whereAll with array and two string'=>['whereAll',[['a','b'],'=',1],'&&(((a)=(1))&&((b)=(2)))'],
        'whereNone with array and two string'=>['whereNone',[['a','b'],'=',1],'&&(!(((a)=(1))||((b)=(2))))'],
        ]);
