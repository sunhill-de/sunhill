<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\ParentObject;
use Sunhill\Tests\TestSupport\Objects\ChildObject;

uses(SunhillDatabaseTestCase::class);

test('Read a childobject with both arrays', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ChildObject::getExpectedStructure());
    ChildObject::prepareDatabase($this);
    $test->load(9);
    
    expect($test->getValue('parent_int'))->toBe(333);
    expect($test->getValue('parent_string'))->toBe('CCC');
    expect($test->getIndexedValue('parent_sarray',1))->toBe(31);
    
    expect($test->getValue('child_int'))->toBe(212);
    expect($test->getValue('child_string'))->toBe('BCD');
    expect($test->getIndexedValue('child_sarray',1))->toBe(210);
});

test('Read a childobject with parent array', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ChildObject::getExpectedStructure());
    ChildObject::prepareDatabase($this);
    $test->load(10);
    
    expect($test->getValue('parent_int'))->toBe(444);
    expect($test->getValue('parent_string'))->toBe('DDD');
    expect($test->getIndexedValue('parent_sarray',1))->toBe(41);
    
    expect($test->getValue('child_int'))->toBe(222);
    expect($test->getValue('child_string'))->toBe('CDE');
    expect($test->getElementCount('child_sarray'))->toBe(0);
});

test('Read a childobject with child array', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ChildObject::getExpectedStructure());
    ChildObject::prepareDatabase($this);
    $test->load(11);
    
    expect($test->getValue('parent_int'))->toBe(555);
    expect($test->getValue('parent_string'))->toBe('EEE');
    expect($test->getElementCount('parent_sarray'))->toBe(0);
    
    expect($test->getValue('child_int'))->toBe(232);
    expect($test->getValue('child_string'))->toBe('DEF');
    expect($test->getIndexedValue('child_sarray',1))->toBe(410);
});

test('Read a childobject with both arrays empty', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ChildObject::getExpectedStructure());
    ChildObject::prepareDatabase($this);
    $test->load(12);
    
    expect($test->getValue('parent_int'))->toBe(666);
    expect($test->getValue('parent_string'))->toBe('FFF');
    expect($test->getElementCount('parent_sarray'))->toBe(0);
    
    expect($test->getValue('child_int'))->toBe(242);
    expect($test->getValue('child_string'))->toBe('EFG');
    expect($test->getElementCount('child_sarray'))->toBe(0);
});

