<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\ParentObject;
use Sunhill\Tests\TestSupport\Objects\ChildObject;
use Sunhill\Tests\TestSupport\Objects\ArrayOnlyChildObject;

uses(SunhillDatabaseTestCase::class);

test('Read a arrayonlychildobject with child arrays', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ArrayOnlyChildObject::getExpectedStructure());
    ArrayOnlyChildObject::prepareDatabase($this);
    $test->load(20);
    
    expect($test->getValue('parent_int'))->toBe(5555);
    expect($test->getValue('parent_string'))->toBe('ERE');
    expect($test->getElementCount('parent_sarray'))->toBe(0);
    
    expect($test->getIndexedValue('child_sarray',1))->toBe(2100);
});

test('Read a childobject with no array', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ArrayOnlyChildObject::getExpectedStructure());
    ArrayOnlyChildObject::prepareDatabase($this);
    $test->load(21);
    
    expect($test->getValue('parent_int'))->toBe(6666);
    expect($test->getValue('parent_string'))->toBe('FRF');
    expect($test->getElementCount('parent_sarray'))->toBe(0);
    
    expect($test->getElementCount('child_sarray'))->toBe(0);
});

