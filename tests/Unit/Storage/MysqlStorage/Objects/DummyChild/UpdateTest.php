<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\DummyChild;

uses(SunhillDatabaseTestCase::class);

require_once(dirname(__FILE__).'/../ObjectHelpers.php');

test('Update a DummyChild with nothing modified', function()
{
    $test = prepareStorage(DummyChild::class, $this);
    pretendLoaded($test, 13, DummyChild::class);
    
    prepareObjectDataset($test, 'DummyChild');
    
    $test->commit();
    
    $this->assertDatabaseHas('objects',
        [
            'id'=>13,
            '_classname'=>'DummyChild',
            '_uuid'=>'11b47be8-05f1-4f7b-8a97-e1e6488dbd44',
            '_created_at'=>'2024-11-14 20:00:00',
            '_updated_at'=>'2024-11-14 20:00:00',
        ]);
    $this->assertDatabaseHas('dummies',['id'=>13,'dummyint'=>999]);
    $this->assertDatabaseHas('dummychildren',['id'=>13,'dummychildint'=>919]);
})->group('update');

test('Update a DummyChild with both modified', function()
{
    $test = prepareStorage(DummyChild::class, $this);
    pretendLoaded($test, 13, DummyChild::class);
    
    prepareObjectDataset($test, 'DummyChild');
    $test->setValue('dummyint',777);
    $test->setValue('dummychildint',797);
    
    $test->commit();
    
    $this->assertDatabaseHas('objects',
        [
            'id'=>13,
            '_classname'=>'DummyChild',
            '_uuid'=>'11b47be8-05f1-4f7b-8a97-e1e6488dbd44',
            '_created_at'=>'2024-11-14 20:00:00',
            '_updated_at'=>'2024-11-14 20:00:00',
        ]);
    $this->assertDatabaseHas('dummies',['id'=>13,'dummyint'=>777]);
    $this->assertDatabaseHas('dummychildren',['id'=>13,'dummychildint'=>797]);
})->group('update');

test('Update a DummyChild only child modified', function()
{
    $test = prepareStorage(DummyChild::class, $this);
    pretendLoaded($test, 13, DummyChild::class);
    
    prepareObjectDataset($test, 'DummyChild');
    $test->setValue('dummyint',777);
    
    $test->commit();
    
    $this->assertDatabaseHas('objects',
        [
            'id'=>13,
            '_classname'=>'DummyChild',
            '_uuid'=>'11b47be8-05f1-4f7b-8a97-e1e6488dbd44',
            '_created_at'=>'2024-11-14 20:00:00',
            '_updated_at'=>'2024-11-14 20:00:00',
        ]);
    $this->assertDatabaseHas('dummies',['id'=>13,'dummyint'=>777]);
    $this->assertDatabaseHas('dummychildren',['id'=>13,'dummychildint'=>919]);
})->group('update');

test('Update a DummyChild only parent modified', function()
{
    $test = prepareStorage(DummyChild::class, $this);
    pretendLoaded($test, 13, DummyChild::class);
    
    prepareObjectDataset($test, 'DummyChild');
    $test->setValue('dummychildint',797);
    
    $test->commit();
    
    $this->assertDatabaseHas('objects',
        [
            'id'=>13,
            '_classname'=>'DummyChild',
            '_uuid'=>'11b47be8-05f1-4f7b-8a97-e1e6488dbd44',
            '_created_at'=>'2024-11-14 20:00:00',
            '_updated_at'=>'2024-11-14 20:00:00',
        ]);
    $this->assertDatabaseHas('dummies',['id'=>13,'dummyint'=>999]);
    $this->assertDatabaseHas('dummychildren',['id'=>13,'dummychildint'=>797]);
})->group('update');

