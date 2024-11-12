<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Tests\Feature\Properties\PooledRecordProperty\Examples\IncludeParentProperty;
use Sunhill\Tests\Feature\Properties\PooledRecordProperty\Examples\IncludeChildProperty;
use Sunhill\Tests\Feature\Properties\PooledRecordProperty\Examples\EmbedParentProperty;
use Sunhill\Tests\Feature\Properties\PooledRecordProperty\Examples\EmbedChildProperty;
use Sunhill\Tests\Feature\Properties\PooledRecordProperty\Examples\DummyPersistentPoolStorage;

uses(SunhillTestCase::class);

test('modify included parent', function()
{
   $test = new IncludeParentProperty();
   $test->delete(1);
   
   expect(count(DummyPersistentPoolStorage::$persistent_data['poolA']))->toBe(1);
});

test('modify included child', function()
{
    $test = new IncludeChildProperty();
    $test->delete(1);

    expect(count(DummyPersistentPoolStorage::$persistent_data['poolA']))->toBe(1);
});

test('modify embedded parent', function()
{
    $test = new EmbedParentProperty();
    $test->delete(1);
    
    expect(count(DummyPersistentPoolStorage::$persistent_data['poolA']))->toBe(1);
});

test('modify embedded child', function()
{
    $test = new EmbedChildProperty();
    $test->delete(1);
    
    expect(count(DummyPersistentPoolStorage::$persistent_data['poolA']))->toBe(1);
    expect(count(DummyPersistentPoolStorage::$persistent_data['poolB']))->toBe(1);
});