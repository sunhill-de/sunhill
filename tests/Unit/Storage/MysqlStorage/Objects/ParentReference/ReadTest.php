<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\ParentObject;
use Sunhill\Tests\TestSupport\Objects\ParentReference;

uses(SunhillDatabaseTestCase::class);

test('Read a parentreference with reference and array', function()
{    
    $test = new MysqlObjectStorage();
    $test->setStructure(ParentReference::getExpectedStructure());
    ParentReference::prepareDatabase($this);
    $test->load(17);
    
    expect($test->getValue('parent_int'))->toBe(1111);
    expect($test->getValue('parent_reference'))->toBe(1);
    expect($test->getElementCount('parent_rarray'))->toBe(3);
    expect($test->getIndexedValue('parent_rarray',1))->toBe(3);    
});

test('Read a parentreference only with array', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ParentReference::getExpectedStructure());
    ParentReference::prepareDatabase($this);
    $test->load(18);
    
    expect($test->getValue('parent_int'))->toBe(2222);
    expect($test->getValue('parent_reference'))->toBe(null);
    expect($test->getElementCount('parent_rarray'))->toBe(1);
    expect($test->getIndexedValue('parent_rarray',0))->toBe(3);    
});

test('Read a parentreference with no references', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ParentReference::getExpectedStructure());
    ParentReference::prepareDatabase($this);
    $test->load(19);
    
    expect($test->getValue('parent_int'))->toBe(3333);
    expect($test->getValue('parent_reference'))->toBe(null);
    expect($test->getElementCount('parent_rarray'))->toBe(0);    
});
