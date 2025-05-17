<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\DummyChild;

uses(SunhillDatabaseTestCase::class);

require_once(dirname(__FILE__).'/../ObjectHelpers.php');

test('Read a dummychild from database', function()
{
    $test = prepareStorage(DummyChild::class, $this);
    $test->load(13);
    
    expect($test->getValue('dummyint'))->toBe(999);
    expect($test->getValue('dummychildint'))->toBe(919);
});

