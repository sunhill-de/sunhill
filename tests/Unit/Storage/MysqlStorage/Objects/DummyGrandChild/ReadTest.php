<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\DummyChild;
use Sunhill\Tests\TestSupport\Objects\DummyGrandChild;

uses(SunhillDatabaseTestCase::class);

require_once(dirname(__FILE__).'/../ObjectHelpers.php');


test('Read a dummygrandchild from database', function()
{
    $test = prepareStorage(DummyGrandChild::class, $this);
    $test->load(15);
    
    expect($test->getValue('dummyint'))->toBe(986);
    expect($test->getValue('dummychildint'))->toBe(979);
    expect($test->getValue('dummygrandchildint'))->toBe(911);
});

