<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;

uses(SunhillDatabaseTestCase::class);

require_once(dirname(__FILE__).'/../ObjectHelpers.php');

test('Read a dummy from database (dummyint)', function()
{
    $test = prepareStorage(Dummy::class, $this);
    $test->load(1);
    
    expect($test->getValue('dummyint'))->toBe(123);
});

test('Read a dummy from database (one entry in _tags)', function()
{
    $test = prepareStorage(Dummy::class, $this);
    $test->load(1);
    
    expect($test->getValue('_tags'))->toBe([1]);
});

test('Read a dummy from database (more _tags)', function()
{
    $test = prepareStorage(Dummy::class, $this);
    $test->load(2);
    
    expect($test->getValue('_tags'))->toBe([1,3]);
});
