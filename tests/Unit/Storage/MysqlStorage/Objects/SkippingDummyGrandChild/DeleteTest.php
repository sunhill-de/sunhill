<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\ParentObject;
use Sunhill\Tests\TestSupport\Objects\SkippingDummyChild;
use Sunhill\Tests\TestSupport\Objects\SkippingDummyGrandChild;

uses(SunhillDatabaseTestCase::class);

require_once(dirname(__FILE__).'/../ObjectHelpers.php');

test('Delete a SkippingDummyGrandChild', function()
{
    $test = prepareStorage(SkippingDummyGrandChild::class, $this);
    
    $test->delete(16);
    
    $this->assertDatabaseMissing('dummies',['id'=>16]);
    $this->assertDatabaseMissing('skippingdummychildren',['id'=>16]);
    $this->assertDatabaseMissing('skippingdummygrandchildren',['id'=>16]);
})->group('delete');

