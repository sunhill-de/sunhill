<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Tests\Feature\Properties\PooledRecordProperty\Examples\IncludeParentProperty;
use Sunhill\Tests\Feature\Properties\PooledRecordProperty\Examples\IncludeChildProperty;
use Sunhill\Tests\Feature\Properties\PooledRecordProperty\Examples\EmbedParentProperty;
use Sunhill\Tests\Feature\Properties\PooledRecordProperty\Examples\EmbedChildProperty;
use Sunhill\Tests\Feature\Properties\PooledRecordProperty\Examples\DummyPersistentPoolStorage;

uses(SunhillTestCase::class);

test('create included parent', function()
{
   $test = new IncludeParentProperty();
   $test->create();
   $test->parent_str = 'ZZZ';
   $test->parent_int = 666;
   $test->commit();
   
   expect(DummyPersistentPoolStorage::$persistent_data['poolA'][2]['parent_str'])->toBe('ZZZ');
   expect(DummyPersistentPoolStorage::$persistent_data['poolA'][2]['parent_int'])->toBe(666);
});

test('create included child', function()
{
    $test = new IncludeChildProperty();
    $test->create();
    $test->parent_str = 'ZZZ';
    $test->parent_int = 666;
    $test->child_str = 'ZAZ';
    $test->commit();

    expect(DummyPersistentPoolStorage::$persistent_data['poolB'][2]['parent_str'])->toBe('ZZZ');
    expect(DummyPersistentPoolStorage::$persistent_data['poolB'][2]['parent_int'])->toBe(666);
    expect(DummyPersistentPoolStorage::$persistent_data['poolB'][2]['child_str'])->toBe('ZAZ');    
});

test('create embedded parent', function()
{
    $test = new EmbedParentProperty();
    $test->create();
    $test->parent_str = 'ZZZ';
    $test->parent_int = 666;
    $test->commit();
    
    expect(DummyPersistentPoolStorage::$persistent_data['poolA'][2]['parent_str'])->toBe('ZZZ');
    expect(DummyPersistentPoolStorage::$persistent_data['poolA'][2]['parent_int'])->toBe(666);
});

test('create embedded child', function()
{
    $test = new EmbedChildProperty();
    $test->create();
    $test->parent_str = 'ZZZ';
    $test->parent_int = 666;
    $test->child_str = 'ZAZ';
    $test->commit();
    
    expect(DummyPersistentPoolStorage::$persistent_data['poolA'][2]['parent_str'])->toBe('ZZZ');
    expect(DummyPersistentPoolStorage::$persistent_data['poolA'][2]['parent_int'])->toBe(666);
    expect(DummyPersistentPoolStorage::$persistent_data['poolB'][2]['child_str'])->toBe('ZAZ');
});