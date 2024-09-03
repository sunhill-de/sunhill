<?php

uses(\Sunhill\Properties\Tests\TestCase::class);
use Sunhill\Properties\Types\TypeCalculated;
use Sunhill\Properties\Properties\Exceptions\NoCallbackSetException;
use Sunhill\Properties\Storage\AbstractStorage;
use Sunhill\Properties\Properties\Exceptions\PropertyNotWriteableException;

it('fails when riting to calculated', function() {
    $storage = Mockery::mock(AbstractStorage::class);
    $test = new TypeCalculated();
    $test->setName('test');
    $test->setStorage($storage);
    $test->setValue('ABC');
})->throws(PropertyNotWriteableException::class);

it('fails when reading with no callback set', function() {
    $test = new TypeCalculated();
    $test->setName('test');
    $dummy = $test->getValue();
})->throws(NoCallbackSetException::class);

it('calls an simple callback and updates storage', function() {
    $storage = Mockery::mock(AbstractStorage::class);
    $storage->shouldReceive('setValue')->once()->with('test','ABC');
    $test = new TypeCalculated(); 
    $test->setName('test')->setStorage($storage);
    $test->setCallback(function() { return 'ABC'; });
    expect($test->getValue())->toBe('ABC');
});

