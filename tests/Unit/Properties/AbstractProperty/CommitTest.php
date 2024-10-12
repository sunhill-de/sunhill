<?php

use Sunhill\Tests\SimpleTestCase;
use Sunhill\Storage\AbstractStorage;
use Sunhill\Tests\TestSupport\Properties\NonAbstractProperty;

uses(SimpleTestCase::class);

test('commit() is passed to the storage', function()
{
    $storage = \Mockery::mock(AbstractStorage::class);
    $storage->expects('commit')->once();

    $test = new NonAbstractProperty();
    $test->setStorage($storage);
    
    $test->commit();
});

test('rollback() is passed to the storage', function()
{
    $storage = \Mockery::mock(AbstractStorage::class);
    $storage->expects('rollback')->once();
    
    $test = new NonAbstractProperty();
    $test->setStorage($storage);
    
    $test->rollback();
});

test('isDirty() is passed to the storage', function()
{
    $storage = \Mockery::mock(AbstractStorage::class);
    $storage->expects('isDirty')->with('test')->once();
    
    $test = new NonAbstractProperty();
    $test->setStorage($storage)->setName('test');
    
    $test->isDirty();
});