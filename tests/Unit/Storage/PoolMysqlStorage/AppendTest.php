<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Storage\PoolMysqlStorage\PoolMysqlStorage;
use Sunhill\Storage\Exceptions\StorageTableMissingException;
use Illuminate\Support\Facades\Schema;
use Sunhill\Storage\Exceptions\IDNotFoundException;

require_once('PrepareStorage.php');

uses(SunhillDatabaseTestCase::class);

test('Append a dummy', function()
{
    $test = new PoolMysqlStorage();
    $test->setStructure(prepareStorage($this, 'dummy'));

    $test->setValue('dummyint',1509);
    fillObjectsDataset($test);
        
    $test->commit();

    $this->assertDatabaseHas('objects',
        [
            'id'=>13,
            '_classname'=>'Dummy',
            '_uuid'=>'11b47be8-05f1-4f7b-8a97-e1e6488dbd44',            
            '_created_at'=>'2024-11-14 20:00:00',
            '_updated_at'=>'2024-11-14 20:00:00',
        ]);
    $this->assertDatabaseHas('dummies',['id'=>13,'dummyint'=>1509]);
});

test('Append a parentobject with array', function()
{
    
    $structure = 
    $test = new PoolMysqlStorage();
    $test->setStructure(prepareStorage($this, 'parentobject'));

    $test->setValue('parent_int',1509);
    $test->setValue('parent_string','ACDC');
    $test->setValue('parent_sarray',[919,929,939]);

    fillObjectsDataset($test);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>13,'parent_int'=>1509,'parent_string'=>'ACDC']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>13,'index'=>0,'element'=>919]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>13,'index'=>1,'element'=>929]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>13,'index'=>2,'element'=>939]);    
});

test('Append a parentobject with empty array', function()
{
    $test = new PoolMysqlStorage();
    $test->setStructure(prepareStorage($this, 'parentobject'));

    $test->setValue('parent_int',1509);
    $test->setValue('parent_string','ACDC');
    $test->setValue('parent_sarray',[]);
    
    fillObjectsDataset($test);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>13,'parent_int'=>1509,'parent_string'=>'ACDC']);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>13]);    
});

test('Append a childobject with both arrays', function()
{    
    $test = new PoolMysqlStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));

    $test->setValue('parent_int',1509);
    $test->setValue('parent_string','ACDC');
    $test->setValue('parent_sarray',[12,34,56]);
    $test->setValue('child_int',5678);
    $test->setValue('child_string','ACAC');
    $test->setValue('child_sarray',[78,90,12]);
    
    fillObjectsDataset($test);
    
    $test->commit();
 
    $this->assertDatabaseHas('parentobjects',['id'=>13,'parent_int'=>1509,'parent_string'=>'ACDC']);
    $this->assertDatabaseHas('childobjects',['id'=>13,'child_int'=>5678,'child_string'=>'ACAC']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>13,'index'=>1,'element'=>34]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>13,'index'=>1,'element'=>90]);
    
});

test('Append a childobject with parent array', function()
{
    $test = new PoolMysqlStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));
    
    $test->setValue('parent_int',1509);
    $test->setValue('parent_string','ACDC');
    $test->setValue('parent_sarray',[12,34,56]);
    $test->setValue('child_int',5678);
    $test->setValue('child_string','ACAC');
    $test->setValue('child_sarray',[]);
    
    fillObjectsDataset($test);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>13,'index'=>1,'element'=>34]);
    $this->assertDatabaseMissing('childobjects_child_sarray',['container_id'=>13]);
});

test('Append a childobject with child array', function()
{
    $test = new PoolMysqlStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));
    
    $test->setValue('parent_int',1509);
    $test->setValue('parent_string','ACDC');
    $test->setValue('parent_sarray',[]);
    $test->setValue('child_int',5678);
    $test->setValue('child_string','ACAC');
    $test->setValue('child_sarray',[78,90,12]);
    
    fillObjectsDataset($test);
    
    $test->commit();
    
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>13]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>13,'index'=>1,'element'=>90]);
});

test('Read a childobject with both arrays empty', function()
{
    $test = new PoolMysqlStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));
    
    $test->setValue('parent_int',1509);
    $test->setValue('parent_string','ACDC');
    $test->setValue('parent_sarray',[]);
    $test->setValue('child_int',5678);
    $test->setValue('child_string','ACAC');
    $test->setValue('child_sarray',[]);
    
    fillObjectsDataset($test);
    
    $test->commit();
    
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>13]);
    $this->assertDatabaseMissing('childobjects_child_sarray',['container_id'=>13]);
});

it('fails when a table is missing', function()
{
    $test = new PoolMysqlStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));
    Schema::drop('parentobjects_parent_sarray');
    
    $test->setValue('parent_int',1509);
    $test->setValue('parent_string','ACDC');
    $test->setValue('parent_sarray',[12,34,56]);
    $test->setValue('child_int',5678);
    $test->setValue('child_string','ACAC');
    $test->setValue('child_sarray',[78,90,12]);
    
    fillObjectsDataset($test);
    
    $test->commit();
})->throws(StorageTableMissingException::class);

