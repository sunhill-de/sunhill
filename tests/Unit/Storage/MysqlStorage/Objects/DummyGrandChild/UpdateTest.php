<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\DummyGrandChild;

uses(SunhillDatabaseTestCase::class);

require_once(dirname(__FILE__).'/../ObjectHelpers.php');

test('Update a dummy with nothing modified', function()
{
    $test = prepareStorage(DummyGrandChild::class, $this);
    
    pretendLoaded($test, 15, DummyGrandChild::class);
    
    prepareObjectDataset($test, 'DummyGrandChild');
    
    $test->commit();
    
    $this->assertDatabaseHas('objects',
        [
            'id'=>15,
            '_classname'=>'DummyGrandChild',
            '_uuid'=>'11b47be8-05f1-4f7b-8a97-e1e6488dbd44',
            '_created_at'=>'2024-11-14 20:00:00',
            '_updated_at'=>'2024-11-14 20:00:00',
        ]);
    $this->assertDatabaseHas('dummies',['id'=>15,'dummyint'=>986]);
    $this->assertDatabaseHas('dummychildren',['id'=>15,'dummychildint'=>979]);
    $this->assertDatabaseHas('dummygrandchildren',['id'=>15,'dummygrandchildint'=>911]);
})->group('update');

test('Update a dummy with all modified', function()
{
    $test = prepareStorage(DummyGrandChild::class, $this);
    
    pretendLoaded($test, 15, DummyGrandChild::class);
    
    prepareObjectDataset($test, 'DummyGrandChild');
    $test->setValue('dummyint',1000);
    $test->setValue('dummychildint',1001);
    $test->setValue('dummygrandchildint',1002);
    
    $test->commit();
    
    $this->assertDatabaseHas('dummies',['id'=>15,'dummyint'=>1000]);
    $this->assertDatabaseHas('dummychildren',['id'=>15,'dummychildint'=>1001]);
    $this->assertDatabaseHas('dummygrandchildren',['id'=>15,'dummygrandchildint'=>1002]);
})->group('update');

test('Update a dummy with only grand child modified', function()
{
    $test = prepareStorage(DummyGrandChild::class, $this);
    
    pretendLoaded($test, 15, DummyGrandChild::class);
    
    prepareObjectDataset($test, 'DummyGrandChild');
    $test->setValue('dummygrandchildint',1002);
    
    $test->commit();
    
    $this->assertDatabaseHas('dummies',['id'=>15,'dummyint'=>986]);
    $this->assertDatabaseHas('dummychildren',['id'=>15,'dummychildint'=>979]);
    $this->assertDatabaseHas('dummygrandchildren',['id'=>15,'dummygrandchildint'=>1002]);
})->group('update');

test('Update a dummy only child modified', function()
{
    $test = prepareStorage(DummyGrandChild::class, $this);
    
    pretendLoaded($test, 15, DummyGrandChild::class);
    
    prepareObjectDataset($test, 'DummyGrandChild');
    $test->setValue('dummychildint',1001);
    
    $test->commit();
    
    $this->assertDatabaseHas('dummies',['id'=>15,'dummyint'=>986]);
    $this->assertDatabaseHas('dummychildren',['id'=>15,'dummychildint'=>1001]);
    $this->assertDatabaseHas('dummygrandchildren',['id'=>15,'dummygrandchildint'=>911]);
})->group('update');

test('Update a dummy with only dummy modified', function()
{
    $test = prepareStorage(DummyGrandChild::class, $this);
    
    pretendLoaded($test, 15, DummyGrandChild::class);
    
    prepareObjectDataset($test, 'DummyGrandChild');
    $test->setValue('dummyint',1000);
    
    $test->commit();
    
    $this->assertDatabaseHas('dummies',['id'=>15,'dummyint'=>1000]);
    $this->assertDatabaseHas('dummychildren',['id'=>15,'dummychildint'=>979]);
    $this->assertDatabaseHas('dummygrandchildren',['id'=>15,'dummygrandchildint'=>911]);
})->group('update');

test('Update a dummy with parent and child modified', function()
{
    $test = prepareStorage(DummyGrandChild::class, $this);
    
    pretendLoaded($test, 15, DummyGrandChild::class);
    
    prepareObjectDataset($test, 'DummyGrandChild');
    $test->setValue('dummyint',1000);
    $test->setValue('dummychildint',1001);
    
    $test->commit();
    
    $this->assertDatabaseHas('dummies',['id'=>15,'dummyint'=>1000]);
    $this->assertDatabaseHas('dummychildren',['id'=>15,'dummychildint'=>1001]);
    $this->assertDatabaseHas('dummygrandchildren',['id'=>15,'dummygrandchildint'=>911]);
})->group('update');

test('Update a dummy with child and grandchild modified', function()
{
    $test = prepareStorage(DummyGrandChild::class, $this);
    
    pretendLoaded($test, 15, DummyGrandChild::class);
    
    prepareObjectDataset($test, 'DummyGrandChild');
    $test->setValue('dummychildint',1001);
    $test->setValue('dummygrandchildint',1002);
    
    $test->commit();
    
    $this->assertDatabaseHas('dummies',['id'=>15,'dummyint'=>986]);
    $this->assertDatabaseHas('dummychildren',['id'=>15,'dummychildint'=>1001]);
    $this->assertDatabaseHas('dummygrandchildren',['id'=>15,'dummygrandchildint'=>1002]);
})->group('update');

test('Update a dummy parent and grandchild modified', function()
{
    $test = prepareStorage(DummyGrandChild::class, $this);
    
    pretendLoaded($test, 15, DummyGrandChild::class);
    
    prepareObjectDataset($test, 'DummyGrandChild');
    $test->setValue('dummyint',1000);
    $test->setValue('dummygrandchildint',1002);
    
    $test->commit();
    
    $this->assertDatabaseHas('dummies',['id'=>15,'dummyint'=>1000]);
    $this->assertDatabaseHas('dummychildren',['id'=>15,'dummychildint'=>979]);
    $this->assertDatabaseHas('dummygrandchildren',['id'=>15,'dummygrandchildint'=>1002]);
})->group('update');
