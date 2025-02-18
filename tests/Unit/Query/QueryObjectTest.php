<?php

namespace Sunhill\Tests\Unit\Query;

use Sunhill\Tests\SunhillTestCase;

uses(SunhillTestCase::class);

test("setStructure() and getStructure()", function()
{
    $test = new QueryObject();
    $structure = new \stdClass();
    $structure->test = 'ABC';
    $test->setStructure($structure);
    expect($test->getStructure()->test)->toBe('ABC');
});

it("fails when getStructure is called with no set structure", function()
{
    $test = new QueryObject();
    $test->getStructure();
})->throws(StructureMissingException::class);

test("hasField() works", function()
{
    $test = new QueryObject();
    $test->setStructure(Dummy::getExpectedStructure());
    expect($test->hasField("dummyint"))->toBe(true);
    expect($test->hasField("nonexisting"))->toBe(false);  
});
     
test("getFieldType() works", function()
{
    $test = new QueryObject();
    $test->setStructure(Dummy::getExpectedStructure());
    expect($test->getFieldType("dummyint"))->toBe("integer");
});
     
test("setFields() with array of fields", function()
{
    $test = new QueryObject();
    $test->setFields(['a','b','c']);
    expect($test->getFields()[1])->toBe('b');
});

test("setFields() with single fields", function()
{
    $test = new QueryObject();
    $test->setFields('a');
    expect($test->getFields()[0])->toBe('a');  
});

test("addWhereStatement() and getWhereStatements()", function
{
     $test = new QueryObject();
     $test->addWhereStatement('and','a','=','abc');
     expect($test->getWhereStatements()[0]->field)->toBe('a');
});

test("setter and getter for offset", function
{
     $test = new QueryObject();
     $test->setOffset(10);

     expect($test->getOffset())->toBe(10);
});

test("setter and getter for limit", function
{
     $test = new QueryObject();
     $test->setLimit(10);

     expect($test->getLimmit())->toBe(10);
});

test("addOrder() and getOrderStatements()", function
{
     $test = new QueryObject();
     $test->addOrder('a','asc');

     expect($test->getOrderStatement()[0]->field)->toBe('a');
     expect($test->getOrderStatement()[0]->dir)->toBe('asc');
}); 

test("addGroupField() and getGroupFields() with single field", function
{
     $test = new QueryObject();
     $test->addGroupField('a');

     expect($test->getGroupFields()[0])->toBe('a');
}); 

test("addGroupField() and getGroupFields() with array of fields", function
{
     $test = new QueryObject();
     $test->addGroupField(['a','b','c']);

     expect($test->getGroupFields()[1])->toBe('b');
}); 

