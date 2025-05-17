<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\ParentObject;

uses(SunhillDatabaseTestCase::class);

require_once(dirname(__FILE__).'/../ObjectHelpers.php');

test('Read a parentobject with array', function()
{    
    $test = prepareStorage(ParentObject::class, $this);
    $test->load(7);
    
    expect($test->getValue('parent_int'))->toBe(111);
    expect($test->getValue('parent_string'))->toBe('AAA');
    expect($test->getIndexedValue('parent_sarray',1))->toBe(11);
    
});

test('Read a parentobject with empty array', function()
{
    $test = prepareStorage(ParentObject::class, $this);
    $test->load(8);
    
    expect($test->getValue('parent_int'))->toBe(222);
    expect($test->getValue('parent_string'))->toBe('BBB');
    expect($test->getElementCount('parent_sarray'))->toBe(0);
    
});
