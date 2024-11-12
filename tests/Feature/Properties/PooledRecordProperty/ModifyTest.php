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
   $test->load(1);
   $test->parent_str = 'ZZZ';
   $test->parent_int = 666;
   $test->commit();
   
   expect(DummyPersistentPoolStorage::$persistent_data['poolA'][1]['parent_str'])->toBe('ZZZ');
   expect(DummyPersistentPoolStorage::$persistent_data['poolA'][1]['parent_int'])->toBe(666);
});

test('modify included child', function()
{
    $test = new IncludeChildProperty();
    $test->load(1);
    $test->parent_str = 'ZZZ';
    $test->parent_int = 666;
    $test->child_str = 'ZAZ';
    $test->commit();

    expect(DummyPersistentPoolStorage::$persistent_data['poolB'][1]['parent_str'])->toBe('ZZZ');
    expect(DummyPersistentPoolStorage::$persistent_data['poolB'][1]['parent_int'])->toBe(666);
    expect(DummyPersistentPoolStorage::$persistent_data['poolB'][1]['child_str'])->toBe('ZAZ');    
});

test('modify embedded parent', function()
{
    $test = new EmbedParentProperty();
    $test->load(1);
    $test->parent_str = 'ZZZ';
    $test->parent_int = 666;
    $test->commit();
    
    expect(DummyPersistentPoolStorage::$persistent_data['poolA'][1]['parent_str'])->toBe('ZZZ');
    expect(DummyPersistentPoolStorage::$persistent_data['poolA'][1]['parent_int'])->toBe(666);
});

test('modify embedded child', function()
{
    $test = new EmbedChildProperty();
    $test->load(1);
    $test->parent_str = 'ZZZ';
    $test->parent_int = 666;
    $test->child_str = 'ZAZ';
    $test->commit();
    
    expect(DummyPersistentPoolStorage::$persistent_data['poolA'][1]['parent_str'])->toBe('ZZZ');
    expect(DummyPersistentPoolStorage::$persistent_data['poolA'][1]['parent_int'])->toBe(666);
    expect(DummyPersistentPoolStorage::$persistent_data['poolB'][1]['child_str'])->toBe('ZAZ');
});