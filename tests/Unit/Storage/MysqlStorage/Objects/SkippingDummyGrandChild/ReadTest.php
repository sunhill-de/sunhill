<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\SkippingDummyGrandChild;

uses(SunhillDatabaseTestCase::class);

require_once(dirname(__FILE__).'/../ObjectHelpers.php');

test('Read a skippingdummygrandchild from database', function()
{
    $test = prepareStorage(SkippingDummyGrandChild::class, $this);
    $test->load(16);
    
    expect($test->getValue('dummyint'))->toBe(976);
    expect($test->getValue('dummygrandchildint'))->toBe(9111);
})->group('read');

