<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\ParentObject;
use Sunhill\Tests\TestSupport\Objects\SkippingDummyChild;

uses(SunhillDatabaseTestCase::class);

test('Delete a SkippingDummyChild', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(SkippingDummyChild::getExpectedStructure());
    SkippingDummyChild::prepareDatabase($this);
    
    $test->delete(14);
    
    $this->assertDatabaseMissing('dummies',['id'=>14]);
    $this->assertDatabaseMissing('skippingdummychildren',['id'=>14]);
})->group('delete');

