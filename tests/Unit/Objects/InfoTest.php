<?php

use Sunhill\Tests\SimpleTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\DummyChild;

uses(SimpleTestCase::class);

test('getObjectName() works on Dummy', function()
{
    expect(Dummy::getObjectName())->toBe('Dummy');
});

test('getObjectName() works on DummyChild', function()
{
    expect(DummyChild::getObjectName())->toBe('DummyChild');    
});

test('getStorageID() works on Dummy', function()
{
    expect(Dummy::getInfo('storage_id'))->toBe('dummies');
    expect(Dummy::getStorageID())->toBe('dummies');
});

test('getStorageID() works on DummyChild', function()
{
    expect(DummyChild::getInfo('storage_id'))->toBe('dummychildren');
    expect(DummyChild::getStorageID())->toBe('dummychildren');
});

