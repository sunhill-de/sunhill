<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\Dummy;

uses(SunhillDatabaseTestCase::class);

test('Update a dummy with dummyint modified', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(Dummy::getExpectedStructure());
    Dummy::prepareDatabase($this);
    
    setProtectedProperty($test, 'id', 1);
    $test->setValue('dummyint',123);
    $test->setValue('dummyint',1509);
    
    $test->setValue('_classname','Dummy');
    $test->setValue('_uuid','11b47be8-05f1-4f7b-8a97-e1e6488dbd44');
    $test->setValue('_read_cap', null);
    $test->setValue('_write_cap', null);
    $test->setValue('_modify_cap', null);
    $test->setValue('_delete_cap', null);
    $test->setValue('_created_at', '2024-11-14 20:00:00');
    $test->setValue('_updated_at', '2024-11-14 20:00:00');
    $test->setValue('_tags',[]);
    $test->setValue('_attributes',[]);
    
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
});

test('Update a dummy with nothing modified', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(Dummy::getExpectedStructure());
    Dummy::prepareDatabase($this);
    
    setProtectedProperty($test, 'id', 1);
    $test->setValue('dummyint',123);
    
    $test->setValue('_classname','Dummy');
    $test->setValue('_uuid','11b47be8-05f1-4f7b-8a97-e1e6488dbd44');
    $test->setValue('_read_cap', null);
    $test->setValue('_write_cap', null);
    $test->setValue('_modify_cap', null);
    $test->setValue('_delete_cap', null);
    $test->setValue('_created_at', '2024-11-14 20:00:00');
    $test->setValue('_updated_at', '2024-11-14 20:00:00');
    $test->setValue('_tags',[]);
    $test->setValue('_attributes',[]);
    
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
});
