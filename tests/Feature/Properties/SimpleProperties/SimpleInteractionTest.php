<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Tests\TestSupport\Storages\DummyStorage;
use Sunhill\Types\TypeFloat;

uses(SunhillTestCase::class);

test('read initialized value', function()
{
    $storage = new DummyStorage();
    $test = new TypeFloat();
    $test->setName('KeyB')->setStorage($storage);
    
    expect($test->getValue())->toBe(3.56);
});