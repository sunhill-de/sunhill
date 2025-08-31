<?php
/**
 * @file InfoTest.php
 * tests: /src/Objects/ORMObject.php
 * free of dependent units: yes
 */

use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\DummyChild;
use Sunhill\Tests\SunhillSimpleTestCase;

uses(SunhillSimpleTestCase::class);

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

