<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Properties\ReferenceProperty;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Storages\DummyStorage;

uses(SunhillDatabaseTestCase::class);

test('Assigning an object', function()
{
    Dummy::prepareDatabase($this);
    $record = new Dummy();
    $record->load(1);
    $storage = new DummyStorage();
    $test = new ReferenceProperty();
    $test->setName('dummy');
    $test->setStorage($storage);
    $test->setValue($record);
    expect($test->getValue()->dummyint)->toBe(123);
});