<?php
/**
 * @file ReadTest.php
 * tests: /src/Storage/AbstractObjectStorage
 * free of dependent units: no, uses makeStdClass()
 */

namespace Sunhill\Tests\Unit\Storage\AbstractObjectStorage;

use Sunhill\Tests\SunhillLaravelTestCase;
use Sunhill\Tests\TestSupport\Objects\ChildObject;
use Sunhill\Facades\Properties;

uses(SunhillLaravelTestCase::class);

function getTestStorageForReading()
{
    Properties::shouldReceive('loadAttribute')->with(1,1)->andReturn(makeStdclass(['name'=>'attribute1','type'=>'string','value'=>'value1']));
    Properties::shouldReceive('loadAttribute')->with(2,1)->andReturn(makeStdclass(['name'=>'attribute2','type'=>'string','value'=>'value2']));
    $test = new DummyAbstractObjectStorage();
    $test::$DataPool = $test::$Data;
    $test->setStructure(ChildObject::getExpectedStructure());
    $test->load(1);
    
    return $test;
}

test('Read a ChildObject reads the object', function()
{
    $test = getTestStorageForReading();
    
    expect($test->getValue('_uuid'))->toBe('de4961ab-f548-4402-8adc-f6d33e80134e');
});

test('Read a ChildObject reads the ParentObject simple field', function()
{
    $test = getTestStorageForReading();
    
    expect($test->getValue('parent_int'))->toBe(123);    
    expect($test->getValue('parent_string'))->toBe('ABC');
});

test('Read a ChildObject reads the ParentObject array', function()
{
    $test = getTestStorageForReading();
    
    expect($test->getIndexedValue('parent_sarray',0))->toBe(321);
    expect($test->getIndexedValue('parent_sarray',1))->toBe(432);
    expect($test->getIndexedValue('parent_sarray',2))->toBe(543);    
});

test('Read a ChildObject reads the ChildObject simple field', function()
{
    $test = getTestStorageForReading();
    
    expect($test->getValue('child_int'))->toBe(111);
    expect($test->getValue('child_string'))->toBe('AAA');
});

test('Read a ChildObject reads the ChildObject array', function()
{
    $test = getTestStorageForReading();
    
    expect($test->getIndexedValue('child_sarray',0))->toBe(322);
    expect($test->getIndexedValue('child_sarray',1))->toBe(433);
    expect($test->getIndexedValue('child_sarray',2))->toBe(544);
});

test('Read a ChildObject reads the tags', function()
{
    $test = getTestStorageForReading();
    
    expect($test->getIndexedValue('_tags',0))->toBe(1);
    expect($test->getIndexedValue('_tags',1))->toBe(2);
});

test('Read a ChildObject reads the attributes', function()
{
    $test = getTestStorageForReading();
    
    expect($test->getIndexedValue('_attributes','attribute1'))->toBe('value1');
    expect($test->getIndexedValue('_attributes','attribute2'))->toBe('value2');
});



