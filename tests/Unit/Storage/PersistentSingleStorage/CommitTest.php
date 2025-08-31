<?php
/**
 * @file CommitTest.php
 * tests: /src/PersistentSingleStorage
 * free of dependent units: yes
 */

use Sunhill\Tests\SunhillSimpleTestCase;
use Sunhill\Tests\TestSupport\Storages\DummyPersistentSingleStorage;

uses(SunhillSimpleTestCase::class);

test('Committing a modified existing storage entry', function()
{
    $test = new DummyPersistentSingleStorage();
    $test->load();
    $test->setValue('str_field','TEST');
    $test->commit();
    
    expect($test::$persistent_data['str_field'])->toBe('TEST');
});

test('Rolling back a modified existing storage entry', function()
{
    $test = new DummyPersistentSingleStorage();
    $test->load();
    $test->setValue('str_field','TEST');
    $test->rollback();
    expect($test->getValue('str_field'))->toBe('ABC');
    expect($test->isDirty())->toBe(false);
});
