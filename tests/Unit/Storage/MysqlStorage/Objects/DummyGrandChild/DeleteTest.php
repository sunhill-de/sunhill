<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\ParentObject;
use Sunhill\Tests\TestSupport\Objects\ChildObject;
use Sunhill\Tests\TestSupport\Objects\ArrayOnlyChildObject;
use Sunhill\Tests\TestSupport\Objects\DummyChild;
use Sunhill\Tests\TestSupport\Objects\DummyGrandChild;

uses(SunhillDatabaseTestCase::class);

require_once(dirname(__FILE__).'/../ObjectHelpers.php');

test('Delete an DummyChild', function()
{
    $test = prepareStorage(DummyGrandChild::class, $this);
    
    $test->delete(15);
    
    $this->assertDatabaseMissing('dummies',['id'=>15]);
    $this->assertDatabaseMissing('dummychildren',['container_id'=>15]);
    $this->assertDatabaseMissing('dummygrandchildren',['container_id'=>15]);
})->group('delete');

