<?php

uses(\Sunhill\Tests\TestCase::class);


use Sunhill\Tests\TestSupport\Properties\NonAbstractProperty;
use Sunhill\Storage\AbstractStorage;
use Sunhill\Properties\AbstractProperty;
use Sunhill\Query\Exceptions\WrongTypeException;
use Sunhill\Query\Exceptions\NotAllowedRelationException;

test('::getAllowedRelations() works', function()
{
   NonAbstractProperty::setAllowedRelations(['A','B']);
   expect(NonAbstractProperty::getAllowedRelations())->toBe(['A','B']);
});

test('::isAllowedRelation() works', function()
{
    NonAbstractProperty::setAllowedRelations(['A','B']);
    expect(NonAbstractProperty::isAllowedRelation('A'))->toBe(true);
    expect(NonAbstractProperty::isAllowedRelation('Z'))->toBe(false);    
});

it('fails if calling not allowed relation', function()
{
    NonAbstractProperty::setAllowedRelations(['A']);
    $test = new NonAbstractProperty();
    $test->testRelation('X',10);
})->throws(NotAllowedRelationException::class);

test('testRelation() works', function($relation, $value, $expect, $property_value = 10)
{
    
    $test = new NonAbstractProperty();
    $test::setAllowedRelations(array_merge(AbstractProperty::EQUALITY,AbstractProperty::SIZE,AbstractProperty::WITHNULL));
    
    if (is_string($expect)) {
        $this->expectException(WrongTypeException::class);
        $test->testRelation($relation, $value);
    } else {
        $storage = \Mockery::mock(AbstractStorage::class);
        $storage->expects('getIsInitialized')->once()->andReturn(true);
        $storage->expects('getValue')->with('test_int')->once()->andReturn($property_value);
        $test->setStorage($storage);
        
        expect($test->testRelation($relation, $value))->toBe($expect);
    }
})->with(
    [
        ['=',10,true],   
        ['==',10,true],
        ['=',11,false],
        ['==',11,false],
        ['!=',11,true],
        ['<>',11,true],
        ['!=',10,false],
        ['<>',10,false],
        ['in',[10,11],true],
        ['in',[11,12],false],
        ['notin',[10,11],false],
        ['notin',[11,12],true],
        ['in',10,true],
        ['in',11,false],
        ['in',new \StdClass(),'except'],        
        ['<',11,true],
        ['<',10,false],
        ['<',9,false],
        ['<=',11,true],
        ['<=',10,true],
        ['<=',9,false],
        ['>',11,false],
        ['>',10,false],
        ['>',9,true],
        ['>=',11,false],
        ['>=',10,true],
        ['>=',9,true],        
        ['isnull',null,true,null],
        ['isnull',null,false],
        ['isnotnull',null,false,null],
        ['isnotnull',null,true],
        
        
    ]);