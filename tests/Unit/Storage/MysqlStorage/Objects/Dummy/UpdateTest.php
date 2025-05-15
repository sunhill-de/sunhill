<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\Dummy;

uses(SunhillDatabaseTestCase::class);

require_once(dirname(__FILE__).'/../ObjectHelpers.php');

test('Update a dummy with nothing modified', function()
{
    $test = prepareStorage(Dummy::class, $this);    
    pretendLoaded($test, 1, Dummy::class);    
    prepareObjectDataset($test, 'Dummy');
    
    $test->commit();
    
    $this->assertDatabaseHas('objects',
        [
            'id'=>1,
            '_classname'=>'Dummy',
            '_uuid'=>'11b47be8-05f1-4f7b-8a97-e1e6488dbd44',
            '_created_at'=>'2024-11-14 20:00:00',
            '_updated_at'=>'2024-11-14 20:00:00',
        ]);
    $this->assertDatabaseHas('dummies',['id'=>1,'dummyint'=>123]);
})->group('update');

test('Update a dummy with dummyint modified', function()
{
    $test = prepareStorage(Dummy::class, $this);   
    pretendLoaded($test, 1, Dummy::class);    
    prepareObjectDataset($test, 'Dummy');    
    $test->setValue('dummyint',1509);
    
    $test->commit();
    
    $this->assertDatabaseHas('objects',
        [
            'id'=>1,
            '_classname'=>'Dummy',
            '_uuid'=>'11b47be8-05f1-4f7b-8a97-e1e6488dbd44',
            '_created_at'=>'2024-11-14 20:00:00',
            '_updated_at'=>'2024-11-14 20:00:00',
        ]);
    $this->assertDatabaseHas('dummies',['id'=>1,'dummyint'=>1509]);
})->group('update');

