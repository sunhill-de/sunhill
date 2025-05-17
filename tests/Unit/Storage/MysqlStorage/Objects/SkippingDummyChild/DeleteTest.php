<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\ParentObject;
use Sunhill\Tests\TestSupport\Objects\SkippingDummyChild;

uses(SunhillDatabaseTestCase::class);

require_once(dirname(__FILE__).'/../ObjectHelpers.php');

test('Delete a SkippingDummyChild', function()
{
    $test = prepareStorage(SkippingDummyChild::class, $this);
    
    $test->delete(14);
    
    $this->assertDatabaseMissing('dummies',['id'=>14]);
    $this->assertDatabaseMissing('skippingdummychildren',['id'=>14]);
})->group('delete');

