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

test('Signature', function($function, $mocks, $params, $expect)
{
    foreach ($mocks as $call => $return) {
        if (is_scalar($return)) {
            Queries::shouldReceive('parseQueryString')->with($call)->once()->andReturn(new $return($call));
        } else {
            Queries::shouldReceive('parseQueryString')->with($call)->once()->andReturn($return());            
        }
    }
    
    $test = new Query();
    $test->$function(...$params);
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))->toBe($expect);
})->with(
    [
    // ================================ offset ========================================
        'Offset: Just a simple integer'=>[
            'offset', 
            [], 
            [5],             
            'select,fields:[],where:[],order:[],group:[],offset:[5],limit:[]'            
        ],        
        'Offset: A callback'=>[
            'offset', 
            [], 
            [function() { return 5; }], 
            'select,fields:[],where:[],order:[],group:[],offset:[5],limit:[]'
        ],
        'Offset: An expression'=>[
            'offset',
            ['5+3'=>function() {
               $node = new BinaryNode('+');
               $node->left(new IntegerNode(5));
               $node->right(new IntegerNode(3));
               return $node;
            }], 
            ["5+3"], 
            'select,fields:[],where:[],order:[],group:[],offset:[(5)+(3)],limit:[]'
         ],
        'Offset: An string expression'=>[
            'offset',
            ["'5'"=>function() { return new StringNode('5'); }],
            ["'5'"],
            'select,fields:[],where:[],order:[],group:[],offset:["5"],limit:[]'
        ],
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
test('limit: Just a simple integer', function()
{
    $test = new Query();
    $test->limit(5);
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))
    ->toBe('select,fields:[],where:[],order:[],group:[],offset:[],limit:[5]');
});

test('limit: A callback', function()
{
    $test = new Query();
    $test->limit(function() { return 5; });
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))
    ->toBe('select,fields:[],where:[],order:[],group:[],offset:[],limit:[5]');
});

test('limit: An expression', function()
{
    $node = new BinaryNode('+');
    $node->left(new IntegerNode(5));
    $node->right(new IntegerNode(3));
    Queries::shouldReceive('parseQueryString')->with("5+3")->once()->andReturn($node);
    $test = new Query();
    $test->limit("5+3");
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))
    ->toBe('select,fields:[],where:[],order:[],group:[],offset:[],limit:[(5)+(3)]');
});

test('limit: An string expression', function()
{
    Queries::shouldReceive('parseQueryString')->with("'5'")->once()->andReturn(new StringNode("5"));
    $test = new Query();
    $test->limit("'5'");
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))
    ->toBe('select,fields:[],where:[],order:[],group:[],offset:[],limit:["5"]');
});

test('limit: A node', function()
{
    $test = new Query();
    $test->limit(new IntegerNode(5));
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))
    ->toBe('select,fields:[],where:[],order:[],group:[],offset:[],limit:[5]');
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
test('Where: simple relation', function()
{
    Queries::shouldReceive('parseQueryString')->with('a')->once()->andReturn(new IdentifierNode('a'));    
    Queries::shouldReceive('parseQueryString')->with('abc')->once()->andReturn(new StringNode('abc'));
    
    $test = new Query();
    $test->where('a','=','abc');
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))->toBe('select,fields:[],where:[(a)=("abc")],order:[],group:[],offset:[],limit:[]');    
});

test('Where: simple relation with integer', function()
{
    Queries::shouldReceive('parseQueryString')->with('a')->once()->andReturn(new IdentifierNode('a'));
    
    $test = new Query();
    $test->where('a','=',123);
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))->toBe('select,fields:[],where:[(a)=(123)],order:[],group:[],offset:[],limit:[]');
});


test('Where: simple relation with float', function()
{
    Queries::shouldReceive('parseQueryString')->with('a')->once()->andReturn(new IdentifierNode('a'));
    
    $test = new Query();
    $test->where('a','=',1.23);
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))->toBe('select,fields:[],where:[(a)=(1.23)],order:[],group:[],offset:[],limit:[]');
});


test('Where: simple relation with boolean', function()
{
    Queries::shouldReceive('parseQueryString')->with('a')->once()->andReturn(new IdentifierNode('a'));
    
    $test = new Query();
    $test->where('a','=',true);
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))->toBe('select,fields:[],where:[(a)=(true)],order:[],group:[],offset:[],limit:[]');
});

test('Where: orwhere simple relation', function()
{
    Queries::shouldReceive('parseQueryString')->with('a')->once()->andReturn(new IdentifierNode('a'));
    Queries::shouldReceive('parseQueryString')->with('abc')->once()->andReturn(new StringNode('abc'));
    
    $test = new Query();
    $test->orWhere('a','=','abc');
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))->toBe('select,fields:[],where:[(a)=("abc")],order:[],group:[],offset:[],limit:[]');
});

test('Where: orwhere simple relation with integer', function()
{
    Queries::shouldReceive('parseQueryString')->with('a')->once()->andReturn(new IdentifierNode('a'));
    
    $test = new Query();
    $test->orWhere('a','=',123);
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))->toBe('select,fields:[],where:[(a)=(123)],order:[],group:[],offset:[],limit:[]');
});

test('Where: orwhere simple relation with float', function()
{
    Queries::shouldReceive('parseQueryString')->with('a')->once()->andReturn(new IdentifierNode('a'));
    
    $test = new Query();
    $test->orWhere('a','=',1.23);
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))->toBe('select,fields:[],where:[(a)=(1.23)],order:[],group:[],offset:[],limit:[]');
});

test('Where: orwhere simple relation with boolean', function()
{
    Queries::shouldReceive('parseQueryString')->with('a')->once()->andReturn(new IdentifierNode('a'));
    
    $test = new Query();
    $test->orWhere('a','=',true);
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))->toBe('select,fields:[],where:[(a)=(true)],order:[],group:[],offset:[],limit:[]');
});

test('Where: whereNot simple relation', function()
{
    Queries::shouldReceive('parseQueryString')->with('a')->once()->andReturn(new IdentifierNode('a'));
    Queries::shouldReceive('parseQueryString')->with('abc')->once()->andReturn(new StringNode('abc'));
    
    $test = new Query();
    $test->whereNot('a','=','abc');
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))->toBe('select,fields:[],where:[!((a)=("abc"))],order:[],group:[],offset:[],limit:[]');
});

test('Where: orWhereNot simple relation', function()
{
    Queries::shouldReceive('parseQueryString')->with('a')->once()->andReturn(new IdentifierNode('a'));
    Queries::shouldReceive('parseQueryString')->with('abc')->once()->andReturn(new StringNode('abc'));
    
    $test = new Query();
    $test->orWhereNot('a','=','abc');
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))->toBe('select,fields:[],where:[!((a)=("abc"))],order:[],group:[],offset:[],limit:[]');
});

test('Where: two where relations', function()
{
    Queries::shouldReceive('parseQueryString')->with('a')->once()->andReturn(new IdentifierNode('a'));
    Queries::shouldReceive('parseQueryString')->with('abc')->once()->andReturn(new StringNode('abc'));
    Queries::shouldReceive('parseQueryString')->with('b')->once()->andReturn(new IdentifierNode('b'));
    Queries::shouldReceive('parseQueryString')->with('def')->once()->andReturn(new StringNode('def'));
    
    $test = new Query();
    $test->where('a','=','abc')->where('b','>','def');
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))->toBe('select,fields:[],where:[((a)=("abc"))&&((b)>("def"))],order:[],group:[],offset:[],limit:[]');
});

test('Where: where and orWhere (simple condition)', function()
{
    Queries::shouldReceive('parseQueryString')->with('a')->once()->andReturn(new IdentifierNode('a'));
    Queries::shouldReceive('parseQueryString')->with('abc')->once()->andReturn(new StringNode('abc'));
    Queries::shouldReceive('parseQueryString')->with('b')->once()->andReturn(new IdentifierNode('b'));
    Queries::shouldReceive('parseQueryString')->with('def')->once()->andReturn(new StringNode('def'));
    
    $test = new Query();
    $test->where('a','=','abc')->orWhere('b','>','def');
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))->toBe('select,fields:[],where:[((a)=("abc"))||((b)>("def"))],order:[],group:[],offset:[],limit:[]');
});

test('Where: where and notWhere (simple condition)', function()
{
    Queries::shouldReceive('parseQueryString')->with('a')->once()->andReturn(new IdentifierNode('a'));
    Queries::shouldReceive('parseQueryString')->with('abc')->once()->andReturn(new StringNode('abc'));
    Queries::shouldReceive('parseQueryString')->with('b')->once()->andReturn(new IdentifierNode('b'));
    Queries::shouldReceive('parseQueryString')->with('def')->once()->andReturn(new StringNode('def'));
    
    $test = new Query();
    $test->where('a','=','abc')->whereNot('b','>','def');
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))->toBe('select,fields:[],where:[((a)=("abc"))&&(!((b)>("def")))],order:[],group:[],offset:[],limit:[]');
});

test('Where: where and orNotWhere (simple condition)', function()
{
    Queries::shouldReceive('parseQueryString')->with('a')->once()->andReturn(new IdentifierNode('a'));
    Queries::shouldReceive('parseQueryString')->with('abc')->once()->andReturn(new StringNode('abc'));
    Queries::shouldReceive('parseQueryString')->with('b')->once()->andReturn(new IdentifierNode('b'));
    Queries::shouldReceive('parseQueryString')->with('def')->once()->andReturn(new StringNode('def'));
    
    $test = new Query();
    $test->where('a','=','abc')->orWhereNot('b','>','def');
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))->toBe('select,fields:[],where:[((a)=("abc"))||(!((b)>("def")))],order:[],group:[],offset:[],limit:[]');
});
