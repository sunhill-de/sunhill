<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\ParentObject;
use Sunhill\Tests\TestSupport\Objects\SkippingDummyChild;
use Sunhill\Tests\TestSupport\Objects\SkippingDummyGrandChild;

uses(SunhillDatabaseTestCase::class);

test('Delete a SkippingDummyGrandChild', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(SkippingDummyGrandChild::getExpectedStructure());
    SkippingDummyGrandChild::prepareDatabase($this);
    
    $test->delete(16);
    
    $this->assertDatabaseMissing('dummies',['id'=>16]);
    $this->assertDatabaseMissing('skippingdummychildren',['id'=>16]);
    $this->assertDatabaseMissing('skippingdummygrandchildren',['id'=>16]);
})->group('delete');

